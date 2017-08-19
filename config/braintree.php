<?php

/*
 * This file is part of Laravel Braintree.
 *
 * (c) Brian Faust <hello@brianfaust.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'sandbox',

    /*
    |--------------------------------------------------------------------------
    | Vimeo Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [

        'main' => [
            'environment' => 'production',
            'merchant_id' => '9nbnzq3mhvjtmcxx',
            'public_key'  => '5t6gh5td3y95j8tr',
            'private_key' => env('BRAINTREE_PK'),
        ],

        'sandbox' => [
            'environment' => 'sandbox',
            'merchant_id' => 'vstc5wp8yg5d3x3j',
            'public_key'  => 'h594dbgcpn7kzr8m',
            'private_key' => env('BRAINTREE_SPK'),
        ],

    ],

];
