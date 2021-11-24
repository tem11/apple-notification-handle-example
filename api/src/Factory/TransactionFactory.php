<?php

namespace App\Factory;

use App\Entity\Transaction;
use App\Exceptions\Transaction\DuplicateTransactionException;
use App\Interfaces\PaymentNotificationInterface;
use App\Repository\TransactionRepository;

/**
 * This class is a part of transactional processing.
 * !DO NOT DO FLUSH OR PERSIST!
 */
class TransactionFactory
{
    public function __construct(
        private TransactionRepository $transactionRepository
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

        return new Transaction(
            referenceId: $paymentNotification->getTransactionId(),
            subscriptionId: $paymentNotification->getSubscriptionId(),
            status: $paymentNotification->getStatus(),
            provider: $providerName,
            expiresAt: $paymentNotification->getExpiresAt()
        );
    }
}