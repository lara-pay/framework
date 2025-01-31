<?php

namespace LaraPay\Framework;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $table;

    protected $fillable = [
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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('larapay.tables.subscriptions', 'larapay_subscriptions');
    }

    protected static function boot()
    {
        parent::boot();
    }

    public function getSuccessUrlAttribute($value)
    {
        return $value ?: config('larapay.success_url', url('/'));
    }

    public function getCancelUrlAttribute($value)
    {
        return $value ?: config('larapay.cancel_url', url('/'));
    }

    public function successUrl()
    {
        return $this->success_url;
    }

    public function cancelUrl()
    {
        return $this->cancel_url;
    }

    public function webhookUrl()
    {
        return route('larapay.webhook', ['gateway_id' => $this->gateway->gateway()->getWebhookIdentifier(), 'subscription_id  ' => $this->id]);
    }
}
