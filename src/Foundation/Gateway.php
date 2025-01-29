<?php

namespace LaraPay\Framework\Foundation;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use LaraPay\Framework\Foundation\GatewayFoundation;

class Gateway extends Model
{
    protected $table = 'larapay_gateways';

    protected $fillable = [
        'alias',
        'identifier',
        'namespace',
        'config',
        'is_active',
        'tag',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'gateway_id');
    }

    public function setConfigAttribute($value)
    {
        $this->attributes['config'] = encrypt($value);
    }

    public function getConfigAttribute($value)
    {
        if (!$value) {
            return [];
        }

        return decrypt($value);
    }

    public static function getInstalledGateways()
    {
        $gateways = [];
        $basePath = app_path('Gateways');

        // Get all directories inside app/Gateways
        $directories = File::directories($basePath);

        foreach ($directories as $directory) {
            $gatewayFile = $directory . '/Gateway.php';

            if (File::exists($gatewayFile)) {
                // Extract class name from directory name
                $gatewayName = basename($directory);
                $class = "App\\Gateways\\$gatewayName\\Gateway";

                if (class_exists($class)) {
                    $instance = (new $class);

                    if ($instance instanceof GatewayFoundation) {
                        $gateways[$instance->getId()] = $class;
                    }
                }
            }
        }

        return $gateways;
    }

    public function pay(Payment $payment)
    {
        $currencies = $this->getCurrencies();
        if (!empty($currencies) AND !in_array($payment->currency, $currencies)) {
            throw new \Exception("Gateway '{$this->alias}' does not support currency {$payment->currency}");
        }

        return $this->gateway()->pay($payment);
    }

    public function gateway()
    {
        return (new $this->namespace);
    }

    public function getCurrencies()
    {
        return $this->gateway()->getCurrencies();
    }
}
