<?php namespace Kattatzu\Sbif\Providers;

use Kattatzu\Sbif\Sbif;
use Illuminate\Support\ServiceProvider;

class SbifServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/config.php' => app()->basePath() . '/config/sbif.php',
        ]);
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Sbif::class, function ($app) {
            $apiKey = config('sbif.key', env('SBIF_API_KEY'));

            return new Sbif($apiKey);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [Sbif::class];
    }
}
