<?php

namespace App\Jobs;

use App\Mail\CreateOrderMail;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendingEmailQueue implements ShouldQueue
{
    use Queueable;

    public $order;
    public $user;
    /**
     * Create a new job instance.
     */
    public function __construct(Order $order,$user)
    {
        $this->user=$user;
        $this->order=$order;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new CreateOrderMail($this->order));
    }
}
