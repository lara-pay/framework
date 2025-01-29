<?php

namespace LaraPay\Framework\Foundation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $table = 'larapay_payments';

    protected $fillable = [
        'token',
        'user_id',
        'gateway_id',
        'description',
        'status',
        'currency',
        'amount',
        'transaction_id',
        'handler',
        'data',
        'paid_at',
    ];

    protected $casts = [
        'data' => 'array',
        'paid_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($payment) {
            $payment->token = $payment->token ?: Str::random(48);
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class);
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function completed($transactionId = null, array $paymentData = []): void
    {
        $this->callHandler('onPaymentCompleted');

        $this->update([
            'status' => 'paid',
            'transaction_id' => $transactionId,
            'data' => $paymentData,
            'paid_at' => now(),
        ]);
    }

    public function declined(): void
    {
        $this->callHandler('onPaymentDeclined');

        $this->update([
            'status' => 'declined',
        ]);
    }

    public function refunded(): void
    {
        $this->callHandler('onPaymentRefunded');

        $this->update([
            'status' => 'refunded',
        ]);
    }

    public function disputed(): void
    {
        $this->callHandler('onPaymentDisputed');

        $this->update([
            'status' => 'disputed',
        ]);
    }

    public function callHandler($method)
    {
        if ($this->handler && method_exists($this->handler, $method)) {
            $this->handler->{$method}($this);
        }
    }

    public function generateLinkForGateway(Gateway $gateway)
    {
        $this->update([
            'gateway_id' => $gateway->id,
        ]);

        $token = Str::random(32);
        Cache::put($token, $this->id, now()->addMinutes(60));

        return route('larapay.pay', ['token' => $token]);
    }

    public function payWith(Gateway $gateway)
    {
        $this->update([
            'gateway_id' => $gateway->id,
        ]);

        return $gateway->pay($this);
    }
}
