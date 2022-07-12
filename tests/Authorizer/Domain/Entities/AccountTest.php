<?php

use Nubank\Authorizer\Domain\Entities\Account;
use Nubank\Authorizer\Domain\Entities\Transaction;
use Nubank\Authorizer\Domain\Exceptions\TransactionException;
use PHPUnit\Framework\TestCase;

class AccountTest extends TestCase
{
    public function testTransactionSumAfterTransactionIsAdded(): void
    {
        $account = new Account(limit: 100, activeCard: true);
        $transaction = new Transaction(merchant: 'Test', amount: 10, time: new DateTime());
        $account->addTransaction($transaction);

        $this->assertEquals(10, $account->getTransactionsSum());
    }

    public function testBalanceAfterTransactionIsAdded(): void
    {
        $account = new Account(limit: 100, activeCard: true);
        $transaction = new Transaction(merchant: 'Test', amount: 10, time: new DateTime());
        $account->addTransaction($transaction);

        $this->assertEquals(90, $account->getBalance());
    }

    public function testAddTransactionWithInsufficientLimit(): void
    {
        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('insufficient-limit');

        $account = new Account(limit: 100, activeCard: true);
        $transaction = new Transaction(merchant: 'Test', amount: 110, time: new DateTime());
        $account->addTransaction($transaction);
    }

    public function testCardIsNotActive(): void
    {
        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('card-not-active');

        $account = new Account(limit: 100, activeCard: false);
        $transaction = new Transaction(merchant: 'Test', amount: 110, time: new DateTime());
        $account->addTransaction($transaction);
    }

    public function testHighFrequencyInterval(): void
    {
        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('high-frequency-small-interval');

        $account = new Account(limit: 100, activeCard: true);

        $now = new DateTime();
        $account->addTransaction(new Transaction(merchant: 'Test', amount: 10, time: $now));
        $account->addTransaction(new Transaction(merchant: 'Test', amount: 20, time: $now->modify('+2 seconds')));
        $account->addTransaction(new Transaction(merchant: 'Test', amount: 30, time: $now->modify('+4 seconds')));
        $account->addTransaction(new Transaction(merchant: 'Test', amount: 15, time: $now->modify('+6 seconds')));
    }

    public function testDoubledTransaction(): void
    {
        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('doubled-transaction');

        $account = new Account(limit: 100, activeCard: true);

        $now = new DateTime();
        $account->addTransaction(new Transaction(merchant: 'Test', amount: 10, time: $now));
        $account->addTransaction(new Transaction(merchant: 'Test', amount: 10, time: $now->modify('+2 seconds')));
    }

    public function testShouldReturnMultipleViolations(): void
    {
        $this->expectException(TransactionException::class);
        $this->expectExceptionMessage('insufficient-limit,doubled-transaction');

        $account = new Account(limit: 10, activeCard: true);

        $now = new DateTime();
        $account->addTransaction(new Transaction(merchant: 'Test', amount: 10, time: $now));
        $account->addTransaction(new Transaction(merchant: 'Test', amount: 10, time: $now->modify('+2 seconds')));
    }
}
