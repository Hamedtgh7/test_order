<?php

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Jobs\SendingEmailQueue;
use App\Mail\CreateOrderMail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class CreateOrderListener implements ShouldQueue
{
    use InteractsWithQueue;
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPlaced $event): void
    {
        dispatch(new SendingEmailQueue($event->order,$event->user));
    }
}
