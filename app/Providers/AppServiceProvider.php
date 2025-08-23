<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use App\Models\Landlord\Room;
use App\Observers\RoomObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Pagination style
        Paginator::useBootstrap();
        Paginator::useBootstrapFive();

        // Gắn observer cho Room
        Room::observe(RoomObserver::class);
    }
}
