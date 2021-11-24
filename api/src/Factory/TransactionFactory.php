<?php

namespace App\Factory;

use App\Entity\Transaction;
use App\Exceptions\Transaction\DuplicateTransactionException;
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
     */
    public function prepareTransaction(
        PaymentNotificationInterface $paymentNotification,
        string $providerName
    ): Transaction {
        if ($this->transactionRepository->transactionExists($paymentNotification->getTransactionId())) {
            throw new DuplicateTransactionException();
        }

        $subscription = $this->subscriptionRepository->findBySubscriptionReference(
            $paymentNotification->getSubscriptionId()
        );
        $transaction = new Transaction(
            referenceId: $paymentNotification->getTransactionId(),
            subscriptionReference: $paymentNotification->getSubscriptionId(),
            status: $paymentNotification->getStatus(),
            provider: $providerName,
            expiresAt: $paymentNotification->getExpiresAt()
        );
        if ($subscription !== null) {
            $transaction->setSubscription($subscription);
            $subscription->addTransaction($transaction);
        }

        return $transaction;
    }
}