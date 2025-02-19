<?php

namespace App\Providers;

use App\Events\OrderPlaced;
use App\Listeners\SendCreateAlarm;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\ServiceProvider;

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
        App::setlocale(Session::get('locale',config('app.locale')));
    }
}
