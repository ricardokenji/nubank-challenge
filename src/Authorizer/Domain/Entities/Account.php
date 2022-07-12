<?php

namespace Nubank\Authorizer\Domain\Entities;

use Nubank\Authorizer\Application\Violations;
use Nubank\Authorizer\Domain\Exceptions\TransactionException;
use DateTime;

class Account
{
    public function __construct(private int $limit, private bool $activeCard, private array $transactions = [])
    {
    }

    /**
     * Add transaction to account if transaction is valid
     * @param Transaction $transaction
     */
    public function addTransaction(Transaction $transaction): void
    {
        $violations = $this->validateTransaction($transaction);

        if (!empty($violations)) {
            throw new TransactionException($violations);
        }

        $this->transactions[] = $transaction;
    }

    /**
     * Get card status
     * @return bool
     */
    public function isActiveCard(): bool
    {
        return $this->activeCard;
    }

    /**
     * Get account balance
     * @return int
     */
    public function getBalance(): int
    {
        return $this->limit - $this->getTransactionsSum();
    }

    /**
     * Get sum of all transactions
     * @return int
     */
    public function getTransactionsSum(): int
    {
        if (empty($this->transactions)) {
            return 0;
        }
        return array_reduce($this->transactions, fn($carry, Transaction $transaction) => $carry + $transaction->getAmount());
    }

    /**
     * Validate if transaction can be added and return an array containing all violations if any
     * @param Transaction $transaction
     * @return array
     */
    private function validateTransaction(Transaction $transaction): array
    {
        $violations = [];
        if (!$this->isActiveCard()) {
            $violations[] = Violations::CardNotActive;
        }
        if ($transaction->getAmount() > $this->getBalance()) {
            $violations[] = Violations::InsufficientLimit;
        }
        if ($this->hasHighFrequencyTransactions(from: $transaction->getTime(), minutes: 2)) {
            $violations[] = Violations::HighFrequencySmallInterval;
        }
        if ($this->hasDoubledTransactions(from: $transaction->getTime(), amount: $transaction->getAmount(), minutes: 2)) {
            $violations[] = Violations::DoubledTransaction;
        }
        return $violations;
    }

    /**
     * Check if transaction violates high frequency interval rule
     * @param DateTime $from
     * @param int $minutes
     * @return bool
     */
    private function hasHighFrequencyTransactions(DateTime $from, int $minutes): bool
    {
        $transactions = array_filter($this->transactions, function (Transaction $transaction) use ($from, $minutes) {
            $interval = $from->getTimestamp() - $transaction->getTime()->getTimestamp();
            return $interval <= $minutes * 60;
        });
        return count($transactions) > 2;
    }

    /**
     * Check if transaction violates doubled transaction rule
     * @param DateTime $from
     * @param $amount
     * @param int $minutes
     * @return bool
     */
    private function hasDoubledTransactions(DateTime $from, $amount, int $minutes): bool
    {
        $transactions = array_filter($this->transactions, function (Transaction $transaction) use ($from, $amount, $minutes) {
            $interval = $from->getTimestamp() - $transaction->getTime()->getTimestamp();
            return $interval <= $minutes * 60 && $transaction->getAmount() == $amount;
        });
        return count($transactions) > 0;
    }
}
