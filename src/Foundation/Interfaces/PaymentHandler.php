<?php

namespace LaraPay\Framework\Foundation\Interfaces;

abstract class PaymentHandler
{
    abstract public function onPaymentCompleted($payment);

    abstract public function onPaymentFailed($payment, $exception);

    abstract public function onPaymentRefunded($payment);
}
