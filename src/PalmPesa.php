<?php

namespace PalmPesa\Payment;

use Illuminate\Support\Facades\Http;
use App\Models\Transaction;

class PalmPesa
{
    protected $baseUrl = 'https://palmpesa.drmlelwa.co.tz/api';
    protected $token = 'UgGnf1bYJb1vC8MoZXa7LDXcWS6sA7mxWR12MaPgr05kDowvakyzP6jBLsbs';

    public function initiatePayment(array $data)
    {
        $response = Http::withoutVerifying()
            ->withToken($this->token)
            ->acceptJson()
            ->post($this->baseUrl . '/pay-via-mobile', $data);

        return $response->json();
    }

    public function checkOrderStatus(string $orderId)
    {
        $response = Http::withoutVerifying()
            ->withToken($this->token)
            ->acceptJson()
            ->post($this->baseUrl . '/order-status', [
                'order_id' => $orderId,
            ]);

        if ($response->successful()) {
            $res = $response->json();
            $data = $res['data'][0] ?? null;

            if ($data) {
                Transaction::where('order_id', $data['order_id'])->update([
                    'status'    => $data['payment_status'] ?? 'unknown',
                    'reference' => $data['reference'] ?? null,
                    'transid'   => $data['transid'] ?? null,
                    'channel'   => $data['channel'] ?? null,
                    'msisdn'    => $data['msisdn'] ?? null,
                    'user_id'   => $data['user_id'] ?? null,
                ]);
            }
        }

        return $response->json();
    }

    public function generatePaymentLink(array $data)
    {
        $response = Http::withoutVerifying()
            ->withToken($this->token)
            ->acceptJson()
            ->post($this->baseUrl . '/process-payment', $data);
    
        $res = $response->json();
    
        if (isset($res['raw']['payment_gateway_url'])) {
            return [
                'status' => true,
                'message' => 'Payment link generated successfully.',
                'link' => $res['raw']['payment_gateway_url']
            ];
        }
    
        return [
            'status' => false,
            'error' => $res['error'] ?? 'Unknown error generating payment link.',
            'raw' => $res,
        ];
    }
    
}