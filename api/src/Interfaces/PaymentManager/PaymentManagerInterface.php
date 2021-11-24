<?php

namespace App\Interfaces\PaymentManager;

interface PaymentManagerInterface
{
    /**
     * @param class-string $notificationClass
     */
    public function supports(string $notificationClass): bool;
    public function verifySignature(string $signature): bool;
}