<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PalmPesa\Payment\PalmPesa;
use App\Models\Transaction;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    protected $palmPesa;

    public function __construct()
    {
        $this->palmPesa = new PalmPesa();
    }

    public function showForm()
    {
        return view('payment.form');
    }

    public function pay(Request $request)
    {
        $request->validate([
            'phone'    => 'required',
            'amount'   => 'required|numeric|min:100',
            'name'     => 'required|string',
            'email'    => 'required|email',
            'address'  => 'required|string',
            'postcode' => 'required|string',
            'user_id'  => 'required|integer',
        ]);
    
        $response = $this->palmPesa->initiatePayment([
            'phone'     => $request->phone,
            'amount'    => $request->amount,
            'name'      => $request->name,
            'email'     => $request->email,
            'address'   => $request->address,
            'postcode'  => $request->postcode,
            'user_id'   => $request->user_id,
        ]);
    
        if (isset($response['order_id'])) {
            $orderId = $response['order_id'];
            $ref     = $response['response']['reference'] ?? null;
            $transid = $response['response']['transid'] ?? null;
    
            Transaction::updateOrCreate(
                ['order_id' => $orderId],
                [
                    'phone'     => $request->phone,
                    'amount'    => $request->amount,
                    'user_id'   => $request->user_id,
                    'status'    => 'PENDING',
                    'reference' => $ref,
                    'transid'   => $transid,
                ]
            );
    
            // 🕒 Wait 20 seconds before checking status
            sleep(30);
    
            // 🔁 Check payment status inline
            try {
                $orderStatusResponse = Http::withHeaders([
                    'Authorization' => 'Bearer UgGnf1bYJb1vC8MoZXa7LDXcWS6sA7mxWR12MaPgr05kDowvakyzP6jBLsbs',
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/json',
                ])->post('https://palmpesa.drmlelwa.co.tz/api/order-status', [
                    'order_id' => $orderId,
                ]);
    
                $data = $orderStatusResponse->json();
    
                if (
                    $orderStatusResponse->successful() &&
                    isset($data['data'][0]) &&
                    $data['data'][0]['payment_status'] === 'COMPLETED'
                ) {
                    $statusData = $data['data'][0];
    
                    Transaction::updateOrCreate(
                        ['order_id' => $statusData['order_id']],
                        [
                            'amount'    => $statusData['amount'],
                            'status'    => $statusData['payment_status'],
                            'channel'   => $statusData['channel'] ?? null,
                            'reference' => $statusData['reference'] ?? null,
                            'msisdn'    => $statusData['msisdn'] ?? null,
                        ]
                    );
                }
            } catch (\Exception $e) {
                Log::error('Inline CheckStatus Error: ' . $e->getMessage());
            }
        }
    
        return redirect()->back()->with('response', $response);
    }
    

    public function payByLink(Request $request)
    {
        $pesa = new PalmPesa();

        $data = [
            'vendor'        => env('PALMPESA_VENDOR_ID'), // your vendor ID
            'order_id'      => $request->transaction_id ?? uniqid('ORDER_'),
            'buyer_name'    => $request->name,
            'buyer_email'   => $request->email,
            'user_id'   => $request->user_id,
            'buyer_phone'   => $request->phone,
            'currency'      => 'TZS',
            'amount'        => $request->amount,
            'redirect_url'  => route('payment.success'), // define this route in web.php
            'cancel_url'    => route('payment.cancel'),  // define this route in web.php
            'webhook'       => route('payment.webhook'), // for automatic payment update
            'no_of_items'   => 1,
        ];

        $result = $pesa->generatePaymentLink($data);

        if ($result['status']) {
            return view('payment.linkView', ['paymentLink' => $result['link']]);
        } else {
            return redirect()->back()->with('error', $result['error']);
        }
    }

}