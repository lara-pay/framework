<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use LaraPay\Framework\Traits\GatewayFunctions;

class Gateway extends Model
{
    use GatewayFunctions;

    protected $table = 'gateways';

    protected $fillable = [
        'display_name',
        'alias',
        'identifier',
        'type',
        'namespace',
        'config',
        'is_active',
        'tag',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
