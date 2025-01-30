<?php

namespace LaraPay\Framework;

use Illuminate\Support\ServiceProvider;
use LaraPay\Framework\Commands\SetupGatewayCommand;
use LaraPay\Framework\Commands\InstallGatewayCommand;
class LaraPayServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->commands([
            SetupGatewayCommand::class,
            InstallGatewayCommand::class
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
