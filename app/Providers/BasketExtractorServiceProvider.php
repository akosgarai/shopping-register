<?php

namespace App\Providers;

use App\Services\BasketExtractorService;
use App\Services\Parsers\SparParserService;

use Illuminate\Support\ServiceProvider;

class BasketExtractorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Services\BasketExtractorService', function () {
            return new BasketExtractorService(config('basketextractor'));
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
