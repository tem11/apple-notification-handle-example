<?php

namespace App\Interfaces;

use App\Entity\Transaction;

interface TransactionHandlerInterface
{
    public function supports(Transaction $transaction): bool;
    public function handle(Transaction $transaction): bool;
}