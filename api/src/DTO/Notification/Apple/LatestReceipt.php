<?php

namespace App\DTO\Notification\Apple;

use JMS\Serializer\Annotation\Type;

class LatestReceipt
{
    public function __construct(
        #[Type('string')]
        private string $item_id,
        #[Type('string')]
        private string $expires_date,
        #[Type('string')]
        private string $transaction_id,
    )
    {}

    /**
     * @return string
     */
    public function getItemId(): string
    {
        return $this->item_id;
    }

    /**
     * @return string
     */
    public function getExpiresDate(): string
    {
        return $this->expires_date;
    }

    /**
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->transaction_id;
    }
}