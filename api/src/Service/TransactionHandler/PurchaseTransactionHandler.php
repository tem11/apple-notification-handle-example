<?php

namespace App\Service\TransactionHandler;

use App\Entity\Subscription;
use App\Entity\Transaction;
use App\Interfaces\NotificationStatus;
use App\Interfaces\TransactionHandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class PurchaseTransactionHandler implements TransactionHandlerInterface
{

    public function __construct(
        private LoggerInterface $logger,
        private EntityManagerInterface $entityManager
    ) {
    }

    public function handle(Transaction $transaction): bool
    {
        if ($transaction->getStatus() !== NotificationStatus::STATUS_PURCHASE) {
            return false;
        }

        $subscription = $transaction->getSubscription();
        if ($subscription !== null) {
            // TODO Add proper situation handler
            $this->logger->error(
                'Attempt to process purchase transaction for existing subscription. Purchase impossible',
                [
                    'transaction_ref' => $transaction->getReferenceId()
                ]
            );
            return false;
        }
        if ($transaction->getExpiresAt()->getTimestamp() < time()) {
            $this->logger->error(
                'Transaction already expired',
                [
                    'transaction_ref' => $transaction->getReferenceId()
                ]
            );

            return false;
        }

        $subscription = new Subscription(
            $transaction->getSubscriptionId(), $transaction->getExpiresAt(), Subscription::STATUS_ACTIVE
        );
        $this->entityManager->persist($subscription);

        $subscription->addTransaction($transaction);
        $transaction->setSubscription($subscription);

        return true;
    }

}