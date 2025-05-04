<?php
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/pay', [PaymentController::class, 'showForm']);
Route::post('/pay', [PaymentController::class, 'pay'])->name('pay.submit');
Route::get('/check-status/{orderId}', [PaymentController::class, 'checkStatus'])->name('pay.check');
Route::post('/palmpesa/pay-by-link', [PaymentController::class, 'payByLink'])->name('pay_by_link');Route::get('/payment-success', function () {
    return 'Payment Success!';
})->name('payment.success');

Route::get('/payment-cancel', function () {
    return 'Payment Cancelled!';
})->name('payment.cancel');

Route::post('/payment-webhook', function (Request $request) {
    // handle automatic update
    Log::info('Webhook: ', $request->all());
})->name('payment.webhook');