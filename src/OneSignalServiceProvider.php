<?php

namespace jmrieger\OneSignal;

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
        /** @noinspection PhpUndefinedFieldInspection */
        $this->app->singleton('onesignal', function ($app) {
            /** @noinspection PhpUndefinedFunctionInspection */
            /** @noinspection PhpUndefinedFunctionInspection */
            /** @noinspection PhpUndefinedFunctionInspection */
            $config = [
                "app_id"        => $app['config']['onesignal']['onesignal_app_id'],
                "rest_api_key"  => $app['config']['onesignal']['onesignal_rest_api_key'],
                "user_auth_key" => $app['config']['onesignal']['onesignal_user_auth_key'],
            ];

            $client = new OneSignalClient($config[ 'app_id' ], $config[ 'rest_api_key' ], $config[ 'user_auth_key' ]);

            return $client;
        });
    }

    public function provides()
    {
        return ['onesignal'];
    }


}
