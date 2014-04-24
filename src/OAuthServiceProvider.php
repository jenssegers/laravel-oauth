<?php namespace Jenssegers\OAuth;

use Illuminate\Support\ServiceProvider;

class OAuthServiceProvider extends ServiceProvider {

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        $this->package('jenssegers/oauth');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bindShared('oauth', function($app)
        {
            return $app->make('Jenssegers\OAuth\OAuth');
        });
    }
}
