<?php

namespace App\Service\TransactionHandler;

use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Interfaces\NotificationStatus;
use App\Interfaces\TransactionHandlerInterface;
use DateTimeImmutable;
use JetBrains\PhpStorm\Pure;
use Psr\Log\LoggerInterface;

class RecurringPaymentTransactionHandler implements TransactionHandlerInterface
{
    private const STATUS_MAPPING = [
        NotificationStatus::STATUS_RENEW => Subscription::STATUS_ACTIVE,
        NotificationStatus::STATUS_FAILED_BILLING => Subscription::STATUS_PENDING_BILLING
    ];

    public function __construct(
        private LoggerInterface $logger,
    ) {
    }

    #[Pure] public function supports(Transaction $transaction): bool
    {
        return isset(self::STATUS_MAPPING[$transaction->getStatus()]);
    }

    public function handle(Transaction $transaction): bool
    {
        if ($transaction->getExpiresAt() < new DateTimeImmutable()) {
            $this->logger->error(
                'Transaction already expired. Renew impossible.',
                ['transaction_ref' => $transaction->getReferenceId()]
            );

            return false;
        }
        $subscription = $transaction->getSubscription();
        if (null === $subscription) {
            // TODO Decide whether we going to create subscription if renew comes through
            $this->logger->error(
                'Subscription not found. Renew impossible',
                ['transaction_ref' => $transaction->getReferenceId()]
            );

            return false;
        }

        if ($subscription->getExpiresAt() > $transaction->getExpiresAt()) {
            $this->logger->warning(
                'Subscription period is greater than provided in renewal transaction',
                ['transaction_ref' => $transaction->getReferenceId()]
            );

            // nothing to do, can store transaction and leave dates untouched
            // @todo investigate if we need to add transaction if expireAt is lower than current amount
            return true;
        }

        $subscription
            ->setExpiresAt(clone $transaction->getExpiresAt())
            ->setStatus(self::STATUS_MAPPING[$transaction->getStatus()])
        ;

        return true;
    }

}