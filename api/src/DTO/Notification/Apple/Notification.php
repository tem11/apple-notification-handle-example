<?php

namespace App\DTO\Notification\Apple;

use App\Exceptions\Notification\CantDetermineStatusException;
use App\Interfaces\DTO\JsonPayloadObject;
use App\Interfaces\PaymentNotificationInterface;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\DateTime;

/**
 * @psalm-immutable
 */
class Notification implements JsonPayloadObject, PaymentNotificationInterface
{
    // @todo Use Enum
    public const NOTIFICATION_TYPE_BUY = 'INITIAL_BUY';
    public const NOTIFICATION_TYPE_RENEW = 'DID_RENEW';
    public const NOTIFICATION_TYPE_FAILED_PAYMENT = 'DID_FAIL_TO_RENEW';
    public const NOTIFICATION_TYPE_CANCEL = 'CANCEL';

    public function __construct(
        #[Assert\NotBlank]
        #[Type('string')]
        #[SerializedName('notification_type')]
        private ?string $notificationType,
        #[Type('string')]
        #[Assert\NotBlank]
        #[SerializedName('auto_renew_product_id')]
        private ?string $autoRenewProductId,
        #[Assert\NotBlank]
        #[Type(Receipt::class)]
        #[SerializedName('unified_receipt')]
        private ?Receipt $unifiedReceipt
    ) {}

    public function getTransactionId(): string
    {
        return $this
            ->unifiedReceipt
            ->getLatestReceiptInfo()
            ->getTransactionId()
        ;
    }

    /**
     * @throws CantDetermineStatusException
     */
    public function getStatus(): string
    {
        return match($this->notificationType) {
            self::NOTIFICATION_TYPE_BUY => PaymentNotificationInterface::STATUS_PURCHASE,
            self::NOTIFICATION_TYPE_RENEW => PaymentNotificationInterface::STATUS_RENEW,
            self::NOTIFICATION_TYPE_FAILED_PAYMENT => PaymentNotificationInterface::STATUS_FAILED_BILLING,
            self::NOTIFICATION_TYPE_CANCEL => PaymentNotificationInterface::STATUS_CANCEL,
            default => throw new CantDetermineStatusException($this->notificationType)
        };
    }

    public function getSubscriptionId(): string
    {
        return $this->autoRenewProductId;
    }

    public function getExpiresAt(): DateTime
    {
        /*
         * @TODO investigate notification structure for
         *  different types and get ExpireDate from it
         */
        return match($this->notificationType) {
            self::NOTIFICATION_TYPE_BUY, self::NOTIFICATION_TYPE_RENEW,
            self::NOTIFICATION_TYPE_FAILED_PAYMENT =>
                new DateTime($this->unifiedReceipt->getLatestReceiptInfo()->getExpiresDate()),
            self::NOTIFICATION_TYPE_CANCEL => new DateTime(),
            default => throw new CantDetermineStatusException()//@todo use proper exception
        };
    }

    /**
     * @return string|null
     */
    public function getNotificationType(): ?string
    {
        return $this->notificationType;
    }

    /**
     * @return string|null
     */
    public function getAutoRenewProductId(): ?string
    {
        return $this->autoRenewProductId;
    }

    /**
     * @return Receipt|null
     */
    public function getUnifiedReceipt(): ?Receipt
    {
        return $this->unifiedReceipt;
    }


}
