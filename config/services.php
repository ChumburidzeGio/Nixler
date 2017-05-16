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

    'facebook' => [
        'client_id' => env('FACEBOOK_APP_ID'),
        'client_secret' => env('FACEBOOK_APP_SECRET'),
        'redirect' => env('FACEBOOK_APP_REDIRECT'),
        'scopes' => ['user_birthday', 'user_location'],
        'fields' => ['name', 'email', 'gender', 'birthday', 'locale', 'timezone', 'updated_time', 'verified', 'location']
    ],

    'vk' => [
        'client_id' => '5068245',
        'client_secret' => '3FC9QzKS17sUkbCiFGoE',
        'version' => '5.62',
    ],

    'yandex' => [
        'trans' => [
            'key' => 'trnsl.1.1.20170324T084419Z.393a0035a180df56.a7710ae0ada5061c0935fd67bb9dc8f639754dc2',
        ],
        'dict' => [
            'key' => 'dict.1.1.20170324T084937Z.34c2244ec10e0112.e7294451e83ac7e1fdb892bddaed91318b4f220a',
        ],
    ],

    'keen' => [
        'projectId' => env('KEEN_PROJECT_ID', ''),
        'writeKey'  => env('KEEN_WRITE_KEY', ''),
        'readKey'   => env('KEEN_READ_KEY', ''),
    ],

    'nexmo' => [
        'key' => env('NEXMO_KEY'),
        'secret' => env('NEXMO_SECRET'),
        'sms_from' => 'Nixler',
    ],

    'telegram-bot-api' => [
        'token' => env('TELEGRAM_BOT_TOKEN')
    ],

    'algolia' => [
        'app_id' => env('ALGOLIA_APP_ID'),
        'app_search_key' => env('ALGOLIA_APP_SEARCH_KEY'),
        'app_admin_key' => env('ALGOLIA_APP_ADMIN_KEY')
    ],
];
