<?php

namespace App\DTO\Notification\Apple;

use App\Exceptions\Notification\CantDetermineStatusException;
use App\Interfaces\DTO\JsonPayloadObject;
use App\Interfaces\NotificationStatus;
use App\Interfaces\PaymentNotificationInterface;
use DateTimeImmutable;
use Exception;
use JMS\Serializer\Annotation\SerializedName;
use JMS\Serializer\Annotation\Type;
use Symfony\Component\Validator\Constraints as Assert;

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
            self::NOTIFICATION_TYPE_BUY => NotificationStatus::STATUS_PURCHASE,
            self::NOTIFICATION_TYPE_RENEW => NotificationStatus::STATUS_RENEW,
            self::NOTIFICATION_TYPE_FAILED_PAYMENT => NotificationStatus::STATUS_FAILED_BILLING,
            self::NOTIFICATION_TYPE_CANCEL => NotificationStatus::STATUS_CANCEL,
            default => throw new CantDetermineStatusException($this->notificationType)
        };
    }

    public function getSubscriptionId(): string
    {
        return $this->autoRenewProductId;
    }

    /**
     * @throws CantDetermineStatusException
     * @throws Exception - DateTime exception
     */
    public function getExpiresAt(): DateTimeImmutable
    {
        /*
         * @TODO investigate notification structure for
         *  different types and get ExpireDate from it
         */
        return match($this->notificationType) {
            self::NOTIFICATION_TYPE_BUY, self::NOTIFICATION_TYPE_RENEW,
            self::NOTIFICATION_TYPE_FAILED_PAYMENT =>
                new DateTimeImmutable(
                    '@'. ceil($this->unifiedReceipt->getLatestReceiptInfo()->getExpiresDate()/1000)
                ),
            self::NOTIFICATION_TYPE_CANCEL => new DateTimeImmutable(),
            default => throw new CantDetermineStatusException()//@todo use proper exception
        };
    }

    public function getSignature(): string
    {
        /**
         * @TODO MOCK, actual processing of signature need to be done
         */
        return base64_encode(
            $this->unifiedReceipt->getLatestReceiptInfo()
                ->getItemId()
        );
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
