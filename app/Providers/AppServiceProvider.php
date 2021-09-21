<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\YoutubeDL\Index as YoutubeDLIndex;
use App\Services\YoutubeDL\Interfaces\YoutubeDLInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(YoutubeDLInterface::class, YoutubeDLIndex::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
