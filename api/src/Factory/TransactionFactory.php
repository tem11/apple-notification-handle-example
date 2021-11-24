<?php

namespace App\Factory;

use App\Entity\Transaction;
use App\Exceptions\Transaction\DuplicateTransactionException;
use App\Exceptions\Transaction\UpdateInactiveSubscriptionException;
use App\Interfaces\PaymentNotificationInterface;
use App\Repository\SubscriptionRepository;
use App\Repository\TransactionRepository;

/**
 * This class is a part of transactional processing.
 * !DO NOT DO FLUSH OR PERSIST!
 */
class TransactionFactory
{
    public function __construct(
        private TransactionRepository $transactionRepository,
        private SubscriptionRepository $subscriptionRepository
    ) {
    }

    /**
     * @throws DuplicateTransactionException
     * @throws UpdateInactiveSubscriptionException
     */
    public function prepareTransaction(
        PaymentNotificationInterface $paymentNotification,
        string $providerName
    ): Transaction {
        if ($this->transactionRepository->transactionExists($paymentNotification->getTransactionId())) {
            throw new DuplicateTransactionException();
        }

        $subscription = $this->subscriptionRepository
            ->findBySubscriptionReference($paymentNotification->getSubscriptionId());
        $transaction = new Transaction(
            referenceId: $paymentNotification->getTransactionId(),
            subscriptionReference: $paymentNotification->getSubscriptionId(),
            status: $paymentNotification->getStatus(),
            provider: $providerName,
            expiresAt: $paymentNotification->getExpiresAt()
        );
        if (null !== $subscription) {
            /** TODO: Refactor THIS! Hook to lifecycle event and explode if object is inactive */
            if (false === $subscription->isActive()) {
                throw new UpdateInactiveSubscriptionException();
            }

            $transaction->setSubscription($subscription);
            $subscription->addTransaction($transaction);
        }

        return $transaction;
    }
}