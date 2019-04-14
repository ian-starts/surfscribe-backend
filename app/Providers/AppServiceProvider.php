<?php

namespace App\Providers;

use App\Factories\CamelCaseJsonResponseFactory;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Http\ResponseFactory;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            ResponseFactory::class,
            function ($app) {
                return new CamelCaseJsonResponseFactory;
            }
        );

    }
}
