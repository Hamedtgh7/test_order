<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOldOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-old-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete oreders older than 30 days.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $deleted=Order::query()->where('created_at','<',Carbon::now()->subDays(30))->delete();
        $this->info("Deleted $deleted orders.");
    }
}
