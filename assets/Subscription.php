<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use LaraPay\Framework\Traits\SubscriptionFunctions;

class Subscription extends Model
{
    use SubscriptionFunctions;

    protected $table = 'subscriptions';

    protected $fillable = [
        'token',
        'user_id',
        'gateway_id',
        'subscription_id',
        'tag',
        'status',
        'name',
        'currency',
        'amount',
        'frequency',
        'grace_period',
        'success_url',
        'cancel_url',
        'handler',
        'data',
        'gateway_data',
        'paid_at',
        'expires_at',
    ];

    protected $casts = [
        'data' => 'array',
        'gateway_data' => 'array',
        'paid_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($subscription) {
            $subscription->token = Str::random(20);
        });
    }
}
