<?php

use Nubank\Authorizer\Application\Commands\CreateTransactionCommand;
use Nubank\Authorizer\Domain\Entities\Account;
use Nubank\Authorizer\Infra\Repository\AccountRepository;
use PHPUnit\Framework\TestCase;

class CreateTransactionCommandTest extends TestCase
{
    private AccountRepository $accountRepository;

    public function setUp(): void
    {
        $this->accountRepository = $this->createMock(AccountRepository::class);
    }

    public function testAddTransaction() {
        $command = new CreateTransactionCommand($this->accountRepository);

        $this->accountRepository->expects($this->exactly(1))
            ->method('get')
            ->willReturn(new Account(limit: 100, activeCard: true));

        $parameters =  ['merchant' => 'Test', 'amount' => 10, 'time' => '2019-02-13T12:00:27.000Z'];
        $result = $command->run($parameters);

        $this->assertEquals(90, $result->accountResponse->availableLimit);
    }

    public function testAccountNotInitialized() {
        $command = new CreateTransactionCommand($this->accountRepository);

        $this->accountRepository->expects($this->exactly(1))
            ->method('get')
            ->willReturn(null);

        $parameters =  ['merchant' => 'Test', 'amount' => 10, 'time' => '2019-02-13T12:00:27.000Z'];
        $result = $command->run($parameters);

        $this->assertEquals('account-not-initialized', $result->violations[0]);
    }


    public function testTransactionViolation() {
        $command = new CreateTransactionCommand($this->accountRepository);

        $this->accountRepository->expects($this->exactly(1))
            ->method('get')
            ->willReturn(new Account(limit: 100, activeCard: true));

        $parameters =  ['merchant' => 'Test', 'amount' => 110, 'time' => '2019-02-13T12:00:27.000Z'];
        $result = $command->run($parameters);

        $this->assertEquals('insufficient-limit', $result->violations[0]);
    }
}
