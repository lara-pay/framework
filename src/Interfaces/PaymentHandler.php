<?php

namespace LaraPay\Framework\Interfaces;

abstract class PaymentHandler
{
    abstract public function onPaymentCompleted($payment);
}
