<?php

namespace Jmrieger\OneSignal;

use Illuminate\Support\ServiceProvider;

class OneSignalServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('onesignal', function ($app) {
            $config = [
                "app_id"            =>  ( env("ONESIGNAL_APP_ID") ?: "" ),
                "rest_api_key"      =>  ( env("ONESIGNAL_REST_API_KEY") ?: ""  ),
                "user_auth_key"     =>  ( env("ONESIGNAL_USER_AUTH_KEY") ?: "" ),
            ];

            $client = new OneSignalClient($config['app_id'], $config['rest_api_key'], $config['user_auth_key']);

            return $client;
        });
    }

    public function provides() {
        return ['onesignal'];
    }


}
