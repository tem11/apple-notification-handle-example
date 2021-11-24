<?php

namespace App\Service\TransactionHandler;

use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Interfaces\NotificationStatus;
use App\Interfaces\TransactionHandlerInterface;
use Psr\Log\LoggerInterface;

class CancelledTransactionHandler implements TransactionHandlerInterface
{

    public function __construct(
        private LoggerInterface $logger
    ) {
    }

    public function supports(Transaction $transaction): bool
    {
        return $transaction->getStatus() === NotificationStatus::STATUS_CANCEL;
    }

    public function handle(Transaction $transaction): bool
    {
        if ($transaction->getStatus() !== NotificationStatus::STATUS_CANCEL ) {
            return false;
        }

        if ($transaction->getSubscription() === null) {
            // TODO Decide whether we going to create subscription if renew comes through
            $this->logger->error(
                'Subscription not found. Skipping cancellation',
                [
                    'transaction_ref' => $transaction->getReferenceId()
                ]
            );

            return false;
        }


        $transaction
            ->getSubscription()
            ->setStatus(Subscription::STATUS_CLOSED)
        ;

        return true;
    }

}