<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Force SSL in production
        if (config('app.env') === 'prd') {
            URL::forceScheme('https');
        }

        $this->bootTKParkSocialite();
    }
    
    private function bootTKParkSocialite()
    {
        $socialite = $this->app->make(Factory::class);
        $socialite->extend('tkpark', function () use ($socialite) {
            $config = config('services.tkpark');
            return $socialite->buildProvider(Socialite\TKParkProvider::class, $config);
        });
    }
}
