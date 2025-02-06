<?php

namespace LaraPay\Framework\Traits;

use App\Models\Gateway;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\User;

trait PaymentFunctions
{
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

    public function webhookUrl(array $params = [])
    {
        $routeParams = array_merge($params, [
            'gateway_id' => $this->gateway->gateway()->getWebhookIdentifier(),
            'payment_token' => $this->token,
        ]);

        return route('larapay.webhook', $routeParams);
    }

    public function callbackUrl(array $params = [])
    {
        $routeParams = array_merge($params, [
            'gateway_id' => $this->gateway->gateway()->getWebhookIdentifier(),
            'payment_token' => $this->token,
        ]);

        return route('larapay.callback', $routeParams);
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

        $this->update([
            'status' => 'paid',
            'transaction_id' => $transactionId,
            'data' => $paymentData,
            'paid_at' => now(),
        ]);

        try {
            $this->callHandler('onPaymentCompleted');
        } catch (\Exception $e) {
            // do nothing
        }
    }
    public function refunded(): void
    {
        $this->update([
            'status' => 'refunded',
        ]);

        try {
            $this->callHandler('onPaymentRefunded');
        } catch (\Exception $e) {
            // do nothing
        }
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