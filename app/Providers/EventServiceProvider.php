<?php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\ProductPublished' => [
            'App\Listeners\UpdateUserStats',
            'App\Listeners\SendProductNotification',
        ],
        'App\Events\ProductDisabled' => [
            'App\Listeners\UpdateUserStats',
        ],
        'App\Events\ProductDeleted' => [
            'App\Listeners\UpdateUserStats',
            'App\Listeners\SendProductNotification',
        ],
        'App\Events\ProductCommented' => [
            'App\Listeners\SendCommentedNotification',
        ],
        'App\Events\ProductLiked' => [
            'App\Listeners\SendLikedNotification',
        ],
        'App\Events\ProductDisliked' => [],
        'App\Events\OrderCreated' => [
            'App\Listeners\UpdateUserStats',
            'App\Listeners\SendPuchasedNotification',
        ],
        'App\Events\UserFollowed' => [
            'App\Listeners\UpdateUserStats',
            'App\Listeners\SendFollowedNotification',
            'App\Listeners\UpdateStreamOnFollowUnfollow',
        ],
        'App\Events\UserUnfollowed' => [
            'App\Listeners\UpdateUserStats',
            'App\Listeners\UpdateStreamOnFollowUnfollow',
        ],
        'Illuminate\Auth\Events\Registered' => [
            'App\Listeners\SendRegisteredNotification',
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
