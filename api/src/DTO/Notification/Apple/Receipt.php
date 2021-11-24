<?php
namespace App\DTO\Notification\Apple;

use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;

class Receipt
{
    public function __construct(
        #[Type(LatestReceipt::class)]
        #[SerializedName('latest_receipt_info')]
        private LatestReceipt $latestReceiptInfo,
    )
    {}

    /**
     * @return LatestReceipt
     */
    public function getLatestReceiptInfo(): LatestReceipt
    {
        return $this->latestReceiptInfo;
    }



}