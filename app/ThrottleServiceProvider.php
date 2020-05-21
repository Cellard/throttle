<?php


namespace Cellard\Throttle;


use Illuminate\Support\ServiceProvider;

class ThrottleServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../database/migrations/' => base_path('/database/migrations')
        ], 'throttle-migrations');
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/throttle.php', 'throttle');
    }
}