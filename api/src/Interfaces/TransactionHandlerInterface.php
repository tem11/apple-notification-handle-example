<?php

namespace App\Interfaces;

use App\Entity\Transaction;

interface TransactionHandlerInterface
{
    public function handle(Transaction $transaction): bool;
}