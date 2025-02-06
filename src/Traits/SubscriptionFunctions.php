<?php

namespace LaraPay\Framework\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Models\Gateway;

trait SubscriptionFunctions
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
            'subscription_token' => $this->token,
        ]);

        return route('larapay.webhook', $routeParams);
    }

    public function callbackUrl(array $params = [])
    {
        $routeParams = array_merge($params, [
            'gateway_id' => $this->gateway->gateway()->getWebhookIdentifier(),
            'subscription_token' => $this->token,
        ]);

        return route('larapay.callback', $routeParams);
    }

    public function subscribeWith($gatewayId)
    {
        if($this->isActive()) {
            throw new \Exception('Subscription is already active.');
        }

        // if payment price is zero, mark it as paid
        if($this->amount == 0) {
            $this->activate();
            return;
        }

        $gateway = Gateway::where('id', $gatewayId)->orWhere('alias', $gatewayId)->first();

        if (!$gateway) {
            throw new \Exception("Could not locate Gateway '{$gatewayId}' by id or alias.");
        }

        $this->update([
            'gateway_id' => $gateway->id,
        ]);

        return $gateway->subscribe($this);
    }

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function activate($subscriptionId = null, array $subscriptionData = [])
    {
        if($this->isActive()) {
            return;
        }

        $this->update([
            'status' => 'active',
            'subscription_id' => $subscriptionId,
            'gateway_data' => $subscriptionData,
            'paid_at' => now(),
            'expires_at' => now()->addDays($this->frequency),
        ]);

        try {
            $this->callHandler('onSubscriptionActivated');
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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function gateway()
    {
        return $this->belongsTo(Gateway::class);
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