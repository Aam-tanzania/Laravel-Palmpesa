<?php
namespace PalmPesa\Payment;

use Illuminate\Support\ServiceProvider;

class PalmPesaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('palmpesa', function () {
            return new PalmPesa();
        });
    }
}
