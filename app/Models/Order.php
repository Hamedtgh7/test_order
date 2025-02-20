<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable=[
        'user_id',
        'status',
        'total_price',
        'invoice_number'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilterByStatus(Builder $query,string $status='')
    {
        return $query->when($status,fn($q)=>$q->where('status',$status));
    }
}
