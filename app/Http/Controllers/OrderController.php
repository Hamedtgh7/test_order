<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Helpers\InvoiceHelper;
use App\Http\Requests\CreateRequest;
use App\Mail\CreateOrderMail;
use App\Models\Order;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;

class OrderController extends Controller
{
    public function filterOrders()
    {
        $userId=Auth::id();

        $orders=Order::query()->where('user_id',$userId)->get();
        $filter_orders=$orders->filter(function ($order){
            return $order->status==='delivered';
        })->sortByDesc('total_price');

        return response()->json($filter_orders);
    }

    public function getUserOrders()
    {
        $userId=Auth::id();

        $orders= Cache::remember("user_orders_{$userId}",now()->addMinutes(10),function() use($userId){
            return Order::query()->where('user_id',$userId)->get();
        });

        // $cache=Cache::get("user_orders_{$userId}");

        return response()->json($orders);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateRequest $request)
    {
        $executed=RateLimiter::attempt(
            'creating-order:'.Auth::user()->id,5,function() use($request){

                $data=$request->validated();
        
                $order=Order::query()->create([
                    'user_id'=>Auth::id(),
                    'total_price'=>$data['total_price'],
                    'invoice_number'=>InvoiceHelper::generatUniqueNUmber()
                ]);
                
                event(new OrderPlaced($order,Auth::user()));
        
                Cache::forget("user_orders_{$order->user_id}");

                return true;
            }
        );
        
        if ($executed){
            return response()->json(['message'=>__('messages.order_created')]);
        }

        return response()->json(['message'=>__('rate_limited')],429);
    }

    public function updateStatus(Request $request,Order $order)
    {
        $data=$request->validate([
            'status'=>'required|in:processing,sent,delivered'
        ]);

        $order->update($data);
        $order->user->notify(new OrderStatusNotification($order));


        broadcast(new OrderStatusUpdated($order));

        Cache::forget("user_orders_{$order->user_id}");


        return response()->json(['message'=>__('order_updated'),'order'=>$order]);
    }

    public function total_prices()
    {
        $cache=Cache::store('file')->remember('total_prices',now()->addHour(1),function(){
            return Order::query()->sum('total_price');
        });

        return response()->json($cache);
    }


    public function uploadReceipt(Request $request, Order $order)
    {
        $request->validate([
            'receipt'=>'required|file|mimes:jpg,png,pdf|max:2048'
        ]);

        if($request->hasFile('receipt')){
            $file=$request->file('receipt');

            $path=$file->store('orders','local');

            $order->receipt=$path;
            $order->save();
        }

        return response()->json(['message'=>__('receipt_uploaded'),'order'=>$order]);
    }
}
