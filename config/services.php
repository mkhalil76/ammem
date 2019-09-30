<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */
    'firebase' => [
        'api_key' => 'AIzaSyDBBzTVa7rgn7fXr-FyuEKux3FH0QgwOP4', // Only used for JS integration
        'auth_domain' => 'ammam-c6db1.firebaseapp.com', // Only used for JS integration
        'database_url' => 'https://ammam-c6db1.firebaseio.com',
        'secret' => 'e3zPcfCUj8wgIj80Nrk5J2Nv0d6L3m8nAOQ8pNW4',
        'storage_bucket' => 'ammam-c6db1.appspot.com', // Only used for JS integration
    ],
    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('SES_KEY'),
        'secret' => env('SES_SECRET'),
        'region' => 'us-east-1',
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'stripe' => [
        'model' => App\User::class,
        'key' => env('STRIPE_KEY'),
        'secret' => env('STRIPE_SECRET'),
    ],

];
