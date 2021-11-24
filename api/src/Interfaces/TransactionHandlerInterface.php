<?php

namespace App\Interfaces;

use App\Entity\Transaction;
use JetBrains\PhpStorm\Pure;

interface TransactionHandlerInterface
{
    #[Pure] public function supports(Transaction $transaction): bool;
    public function handle(Transaction $transaction): bool;
}