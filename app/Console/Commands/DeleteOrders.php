<?php

namespace App\Console\Commands;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:delete-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deleting Orders older than 30 days.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $orders=Order::query()->where('created_at','<',Carbon::now()->subHours(24))->delete();

        $this->info("$orders deleted.");
    }
}
