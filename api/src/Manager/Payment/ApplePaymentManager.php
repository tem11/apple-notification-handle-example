<?php

namespace App\Manager\Payment;

use App\Interfaces\PaymentManager\PaymentManagerInterface;

class ApplePaymentManager implements PaymentManagerInterface
{
    public const NAME = 'apple';

    public function verifySignature(string $signature): bool
    {
        // @TODO Implement
        return true;
    }

    public function supports(string $notificationClass): bool
    {
        // TODO: Implement supports() method.
        return true;
    }

    public function getName(): string
    {
        return self::NAME;
    }

}