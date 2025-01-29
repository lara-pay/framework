<?php

namespace LaraPay\Framework;

use Illuminate\Support\ServiceProvider;

class LaraPayServiceProvider extends ServiceProvider
{
    public function boot()
    {
        // load the migrations
        $this->loadMigrationsFrom(__DIR__ . '/Migrations');
    }

    public function register()
    {

    }
}
