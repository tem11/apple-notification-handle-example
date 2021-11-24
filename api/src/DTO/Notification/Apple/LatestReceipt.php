<?php

namespace App\DTO\Notification\Apple;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class LatestReceipt
{
    public function __construct(
        #[Type('string')]
        #[SerializedName('item_id')]
        private string $itemId,
        #[Type('string')]
        #[SerializedName('expires_date')]
        private string $expiresDate,
        #[Type('string')]
        #[SerializedName('transaction_id')]
        private string $transactionId,
    )
    {}

    /**
     * @return string
     */
    public function getItemId(): string
    {
        return $this->itemId;
    }

    /**
     * @return string
     */
    public function getExpiresDate(): string
    {
        return $this->expiresDate;
    }

    /**
     * @return string
     */
    public function getTransactionId(): string
    {
        return $this->transactionId;
    }
}