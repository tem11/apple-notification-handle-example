<?php

namespace App\Manager;

use App\Entity\Transaction;
use App\Interfaces\TransactionHandlerInterface;
use App\Service\TransactionHandler\PurchaseTransactionHandler;

/**
 * This class is a part of transactional processing.
 * !DO NOT DO FLUSH OR PERSIST!
 */
class SubscriptionManager
{
    /**
     * @var TransactionHandlerInterface[]
     */
    private array $transactionHandlers = [];
    public function __construct(
        PurchaseTransactionHandler $purchaseTransactionHandler
    ) {
        $this->transactionHandlers[] = $purchaseTransactionHandler;
    }

    public function processTransaction(
        Transaction $transaction
    ): bool
    {
        $processingHappened = false;
        foreach ($this->transactionHandlers as $transactionHandler) {
            if ($transactionHandler->handle($transaction)) {
                $processingHappened = true;
            }
        }


        return $processingHappened;
    }
}