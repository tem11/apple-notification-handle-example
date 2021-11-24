<?php

namespace App\Service\TransactionHandler;

use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Interfaces\NotificationStatus;
use App\Interfaces\TransactionHandlerInterface;
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

    public function handle(Transaction $transaction): bool
    {
        if (!isset(self::STATUS_MAPPING[$transaction->getStatus()])) {
            return false;
        }

        if ($transaction->getExpiresAt()->getTimestamp() < time()) {
            $this->logger->error(
                'Transaction already expired. Renew impossible.',
                [
                    'transaction_ref' => $transaction->getReferenceId()
                ]
            );

            return false;
        }

        if ($transaction->getSubscription() === null) {
            // TODO Decide whether we going to create subscription if renew comes through
            $this->logger->error(
                'Subscription not found. Renew impossible',
                [
                    'transaction_ref' => $transaction->getReferenceId()
                ]
            );

            return false;
        }

        if ($transaction->getSubscription()->getExpiresAt()->getTimestamp() > $transaction->getExpiresAt()) {
            // TODO Decide whether we going to create subscription if renew comes through
            $this->logger->warning(
                'Subscription period is greater than renewal',
                [
                    'transaction_ref' => $transaction->getReferenceId()
                ]
            );

            // nothing to do, can store transaction and leave dates untouched
            return true;
        }


        $transaction
            ->getSubscription()
            ->setExpiresAt(
                // clone to make Doctrine happy
                clone $transaction->getExpiresAt()
            )
            ->setStatus(self::STATUS_MAPPING[$transaction->getStatus()])
        ;

        return true;
    }

}