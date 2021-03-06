<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application. Just store away!
    |
    */

    'default' => 'local',

    /*
    |--------------------------------------------------------------------------
    | Default Cloud Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Many applications store files both locally and in the cloud. For this
    | reason, you may specify a default "cloud" driver here. This driver
    | will be bound as the Cloud disk implementation in the container.
    |
    */

    'cloud' => 's3',

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Here you may configure as many filesystem "disks" as you wish, and you
    | may even configure multiple disks of the same driver. Defaults have
    | been setup for each driver as an example of the required options.
    |
    | Supported Drivers: "local", "ftp", "s3", "rackspace"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL').'/storage',
            'visibility' => 'public',
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_KEY'),
            'secret' => env('AWS_SECRET'),
            'region' => env('AWS_REGION'),
            'bucket' => env('AWS_BUCKET'),
        ],

        'google' => [
            'driver' => 'google',
            'clientId' => env('GOOGLE_DRIVE_CLIENT_ID'),
            'clientSecret' => env('GOOGLE_DRIVE_CLIENT_SECRET'),
            'refreshToken' => env('GOOGLE_DRIVE_REFRESH_TOKEN'),
            'folderId' => env('GOOGLE_DRIVE_FOLDER_ID'),
        ],

    ],


    'media' => [
        'avatar' => [
            'default' => public_path('img/person.png'),
            'sizes' => [
                'profile' => [120, 120],
                'nav' => [25, 25],
                'aside' => [80, 80],
                'product' => [80, 80],
                'comments' => [40, 40],
                'message' => [38, 38],
            ]
        ],
        'cover' => [
            'default' => public_path('img/meta.png'),
            'sizes' => [
                'profile' => [1140, 130],
                'product' => [400, 80],
            ]
        ],
        'media' => [
            'default' => public_path('img/image.jpg'),
            'sizes' => [
                'user_photos' => [300, 150],
                'thumb' => [100, 100],
                'thumb_s' => [50, 50],
                'order_cover' => [460, 460],
                'full' => [null, 600],
            ] 
        ],
        'product' => [
            'default' => public_path('img/image.jpg'),
            'sizes' => [
                'full' => [null, 600],
                'short-card' => [300, 370],
                'similar' => [60, 60],
                'comment-attachment' => [530, null],
            ]
        ],
        'collection' => [
            'default' => public_path('img/image.jpg'),
            'sizes' => [
                'stream' => [375, 250],
            ]
        ]
    ]

];
