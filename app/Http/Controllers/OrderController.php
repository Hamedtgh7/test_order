<?php

namespace App\Http\Controllers;

use App\Events\OrderPlaced;
use App\Events\OrderStatusUpdated;
use App\Http\Requests\CreateOrderRequest;
use App\Mail\CreateOrderMail;
use App\Models\Order;
use App\Notifications\OrderStatusNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
require_once app_path('Helpers/helpers.php');

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $status=$request->query('status');
        // $cache=Cache::get("user_orders_{$userId}");

        return response()->json(Cache::remember("user_orders_".Auth::id()."_status_{$status}",now()->addMinutes(10),function() use($status){
            return Order::filterByStatus($status)
            ->where('user_id',Auth::id())->paginate()
            ->toArray();
        }));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateOrderRequest $request)
    {
        $executed=RateLimiter::attempt(
            'creating-order:'.Auth::user()->id,5,function() use($request,&$order){

                $data=$request->validated();
        
                $order=Order::query()->create([
                    'user_id'=>Auth::id(),
                    'total_price'=>$data['total_price'],
                    'invoice_number'=>generateUniqueNumber()
                ]);

                $receipt=generateReceipt($order);

                $order->receipt=$receipt;
                $order->save();
                
                event(new OrderPlaced($order,Auth::user()));
        
                Cache::forget("user_orders_{$order->user_id}");

                return true;
            }
        );
        
        if ($executed){
            return response()->json(['message'=>__('messages.order_created'),'order'=>$order]);
        }

        return response()->json(['message'=>__('rate_limited')],429);
    }

    public function update(Request $request,Order $order)
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

    public function totalPrices(Request $request)
    {
        $start=$request->query('start_date');
        $end=$request->query('end_date');

        $key="total_prices_{$start}_{$end}";

        $cache=Cache::store('file')->remember("$key",now()->addHour(1),function() use($start,$end){
            return Order::query()
            ->when($start, fn($query)=>$query->whereDate('created_at','>=',$start))
            ->when($end, fn($query)=>$query->whereDate('created_at','<=',$end))
            ->sum('total_price');
        });

        return response()->json(['total_prices'=>$cache]);
    }
}
