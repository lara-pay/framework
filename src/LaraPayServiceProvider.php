<?php

namespace LaraPay\Framework;

use Illuminate\Support\ServiceProvider;
use LaraPay\Framework\Commands\SetupGatewayCommand;
use LaraPay\Framework\Commands\InstallGatewayCommand;
use LaraPay\Framework\Commands\UpdateGatewayCommand;
use LaraPay\Framework\Commands\ListGatewayCommand;

class LaraPayServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../../assets/2025_1_29_000000_create_larapay_tables.php' => database_path('migrations/2025_1_29_000000_create_larapay_tables.php'),
            __DIR__.'/../../assets/Gateway.php' => app_path('Models/Gateway.php'),
            __DIR__.'/../../assets/Payment.php' => app_path('Models/Payment.php'),
            __DIR__.'/../../assets/Subscription.php' => app_path('Models/Subscription.php'),
        ], 'larapay');

        $this->commands([
            SetupGatewayCommand::class,
            InstallGatewayCommand::class,
            UpdateGatewayCommand::class,
            ListGatewayCommand::class,
        ]);

        // load the config file
        $this->mergeConfigFrom(__DIR__ . '/Config/larapay.php', 'larapay');

        // load the migrations
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');

        // load the routes
        $this->loadRoutesFrom(__DIR__ . '/routes.php');
    }

    public function register()
    {

    }
}
