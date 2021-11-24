<?php

namespace App\Manager;

use App\Entity\Transaction;
use App\Exceptions\Transaction\CantHandleTransactionException;
use App\Interfaces\TransactionHandlerInterface;
use App\Service\TransactionHandler\CancelledTransactionHandler;
use App\Service\TransactionHandler\PurchaseTransactionHandler;
use App\Service\TransactionHandler\RecurringPaymentTransactionHandler;

/**
 * This class is a part of transactional processing.
 * !DO NOT DO FLUSH OR PERSIST!
 */
class SubscriptionManager
{
    /**
     * @var TransactionHandlerInterface[]
     */
    private array $transactionHandlers;

    /** @TODO: Use compilerPass & tag services to insert as array and decouple from constructor */
    public function __construct(
        PurchaseTransactionHandler $purchaseTransactionHandler,
        RecurringPaymentTransactionHandler $recurringPaymentTransactionHandler,
        CancelledTransactionHandler $cancelledTransactionHandler
    ) {
        $this->transactionHandlers = [
            $purchaseTransactionHandler,
            $recurringPaymentTransactionHandler,
            $cancelledTransactionHandler,
        ];
    }

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