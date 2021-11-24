<?php

namespace App\Interfaces;

use DateTimeImmutable;
use Symfony\Component\Validator\Constraints\DateTime;

interface PaymentNotificationInterface extends NotificationStatus
{
    public function getTransactionId(): string;

    // @TODO Return PHP8 Enum
    public function getStatus(): string;

    public function getSubscriptionId(): string;

    public function getExpiresAt(): DateTimeImmutable;
    public function getSignature(): string;
}