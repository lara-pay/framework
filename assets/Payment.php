<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LaraPay\Framework\Traits\PaymentFunctions;

class Payment extends Model
{
    use PaymentFunctions;

    protected $table = 'payments';

    protected $fillable = [
        'token',
        'user_id',
        'gateway_id',
        'tag',
        'description',
        'status',
        'currency',
        'amount',
        'transaction_id',
        'success_url',
        'cancel_url',
        'handler',
        'data',
        'gateway_data',
        'paid_at',
    ];

    protected $casts = [
        'data' => 'array',
        'gateway_data' => 'array',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            $payment->token = Str::random(20);
        });
    }
}
