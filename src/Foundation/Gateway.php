<?php

namespace LaraPay\Framework\Foundation;

use Illuminate\Database\Eloquent\Model;

class Gateway extends Model
{
    protected $table = 'larapay_gateways';

    protected $fillable = [
        'alias',
        'identifier',
        'namespace',
        'config',
        'is_active',
    ];

    protected $casts = [
        'config' => 'array',
        'is_active' => 'boolean',
    ];

    public function payments()
    {
        return $this->hasMany(Payment::class, 'gateway_id');
    }
}
