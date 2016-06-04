<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = new Dotenv\Dotenv(__DIR__."/../");
$dotenv->load();

$client = new Jmrieger\OneSignal\OneSignalClient(
    getenv('ONESIGNAL_APP_ID'),
    getenv('ONESIGNAL_REST_API_KEY'),
    getenv('ONESIGNAL_USER_AUTH_KEY')
);