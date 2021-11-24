<?php

namespace App\Interfaces;

use Symfony\Component\Validator\Constraints\DateTime;

interface PaymentNotificationInterface
{
    // @TODO Use PHP8 Enums
    public const STATUS_PURCHASE = 'purchase';
    public const STATUS_RENEW = 'renew';
    public const STATUS_CANCEL = 'cancel';
    public const STATUS_FAILED_BILLING = 'failed_billing';

    public function getTransactionId(): string;

    // @TODO Return PHP8 Enum
    public function getStatus(): string;

    public function getSubscriptionId(): string;
    public function getExpiresAt(): DateTime;
}