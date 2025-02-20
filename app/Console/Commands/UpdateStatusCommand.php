<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class UpdateStatusCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:update-status-to-sent';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Statuses from processing to sent after 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders=Order::query()->where('status','processing')
        ->where('created_at','<',Carbon::now()->subHours(24))
        ->update(['status'=>'sent']);

        $this->info("$orders updated.");
    }
}
