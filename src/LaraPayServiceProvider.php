<?php

namespace LaraPay\Framework;

use Illuminate\Support\ServiceProvider;

class LaraPayServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // load the config file
        $this->mergeConfigFrom(__DIR__ . '/Config/larapay.php', 'larapay');

        // load the migrations
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }

    public function register()
    {

    }
}
