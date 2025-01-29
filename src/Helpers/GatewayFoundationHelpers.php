<?php

namespace LaraPay\Framework\Helpers;

use Illuminate\Support\Str;

trait GatewayFoundationHelpers
{
    /**
     * Get identifier of the gateway.
     *
     * @return string
     */
    public function getId(): string
    {
        if (!isset($this->identifier)) {
            throw new \Exception('The gateway identifier is not set.');
        }

        return Str::slug($this->identifier);
    }
}
