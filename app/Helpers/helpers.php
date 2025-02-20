<?php

use App\Models\Order;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

if (! function_exists('generateUniqueNumber')){
    function generateUniqueNumber() :string
    {
        $time=now()->format('YmdHis');
        $random=Str::upper(Str::random(6));
        return "inv-{$time}-{$random}";
    }
}

if (! function_exists('generateReceipt')){
    function generateReceipt(Order $order) :string
    {
        $content ="Invoice Number: {$order->invoice_number}\n";
        $content .="Total Price: {$order->total_price}\n";
        $content .="Status: {$order->status}\n";
        $content .="Date: ". now()->toDateTimeString()."\n";

        $path="orders/order_{$order->id}.txt";

        Storage::disk('local')->put($path,$content);

        return $path;
    }
}