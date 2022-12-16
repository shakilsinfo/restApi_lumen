<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\WalletInterface;
use App\ApiResources\WalletResource;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(WalletInterface::class, WalletResource::class);
    }
}
