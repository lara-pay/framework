<?php

namespace LaraPay\Framework;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $table;

    protected $fillable = [
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

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->table = config('larapay.tables.payments', 'larapay_payments');
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
        return route('larapay.webhook', ['gateway_id' => $this->gateway->gateway()->getWebhookIdentifier(), 'payment_id' => $this->id]);
    }

    public function callbackUrl()
    {
        return route('larapay.callback', ['gateway_id' => $this->gateway->gateway()->getCallbackIdentifier(), 'payment_id' => $this->id]);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class);
    }

    public function total()
    {
        return number_format($this->amount, 2);
    }

    public function isPaid()
    {
        return $this->status === 'paid';
    }

    public function completed($transactionId = null, array $paymentData = []): void
    {
        if($this->isPaid()) {
            return;
        }

        $this->callHandler('onPaymentCompleted');

        $this->update([
            'status' => 'paid',
            'transaction_id' => $transactionId,
            'data' => $paymentData,
            'paid_at' => now(),
        ]);
    }
    public function refunded(): void
    {
        $this->callHandler('onPaymentRefunded');

        $this->update([
            'status' => 'refunded',
        ]);
    }

    public function callHandler($method)
    {
        if ($this->handler && method_exists($this->handler, $method)) {
            $this->handler->{$method}($this);
        }
    }

    public function generateLinkForGateway($gatewayId)
    {
        $gateway = Gateway::where('id', $gatewayId)->orWhere('alias', $gatewayId)->first();

        if (!$gateway) {
            throw new \Exception("Could not locate '{$gatewayId}' by id or alias.");
        }

        $this->update([
            'gateway_id' => $gateway->id,
        ]);

        $token = Str::random(32);
        Cache::put($token, $this->id, now()->addMinutes(60));

        return route('larapay.pay', ['token' => $token]);
    }

    public function payWith($gatewayId)
    {
        if($this->isPaid()) {
            throw new \Exception('This payment has already been paid.');
        }

        // if payment price is zero, mark it as paid
        if($this->amount == 0) {
            $this->completed();
            return;
        }

        $gateway = Gateway::where('id', $gatewayId)->orWhere('alias', $gatewayId)->first();

        if (!$gateway) {
            throw new \Exception("Could not locate '{$gatewayId}' by id or alias.");
        }

        $this->update([
            'gateway_id' => $gateway->id,
        ]);

        return $gateway->pay($this);
    }

    public function data(string $key, $default = null)
    {
        return $this->data[$key] ?? $default;
    }

    public function addData(string $key, $value)
    {
        $this->data[$key] = $value;
        $this->save();
    }
}
