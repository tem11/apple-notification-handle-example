<?php

namespace App\Manager;

use App\Entity\Transaction;
use App\Exceptions\Transaction\CantHandleTransactionException;
use App\Interfaces\TransactionHandlerInterface;

/**
 * This class is a part of transactional processing.
 * !DO NOT DO FLUSH OR PERSIST!
 */
class SubscriptionManager
{
    /**
     * @param iterable<TransactionHandlerInterface> $transactionHandlers
     */
    public function __construct(
        private iterable $transactionHandlers
    ) {}

    /**
     * @throws CantHandleTransactionException
     */
    public function processTransaction(Transaction $transaction): bool
    {
        foreach ($this->transactionHandlers as $transactionHandler) {
            if ($transactionHandler->supports($transaction)) {
                return $transactionHandler->handle($transaction);
            }
        }

        throw new CantHandleTransactionException();
    }
}