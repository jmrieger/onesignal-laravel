<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OneSignal App ID
    |--------------------------------------------------------------------------
    |
    | The OneSignal app to send notifications for by default
    |
    */
    'onesignal_app_id'          => env('ONESIGNAL_APP_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | OneSignal REST API Key
    |--------------------------------------------------------------------------
    |
    | The API key to use the OneSignal API
    |
    */
    'onesignal_rest_api_key'    => env('ONESIGNAL_REST_API_KEY', ''),


    /*
    |--------------------------------------------------------------------------
    | OneSignal User Auth Key
    |--------------------------------------------------------------------------
    |
    | A OneSignal User authentication key, used to create apps in OneSignal
    |
    */
    'onesignal_user_auth_key'          => env('ONESIGNAL_USER_AUTH_KEY', ''),
];