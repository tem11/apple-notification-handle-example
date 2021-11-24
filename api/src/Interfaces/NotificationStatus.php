<?php

namespace App\Interfaces;

interface NotificationStatus
{

    // @TODO Use PHP8 Enums
    public const STATUS_PURCHASE = 'purchase';
    public const STATUS_RENEW = 'renew';
    public const STATUS_CANCEL = 'cancel';
    public const STATUS_FAILED_BILLING = 'failed_billing';

}