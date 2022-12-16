<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\WalletInterface;
use App\ApiResources\WalletResource;
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
   

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(WalletInterface::class, WalletResource::class);
    }
     public function boot()
    {
        //
    }
}
