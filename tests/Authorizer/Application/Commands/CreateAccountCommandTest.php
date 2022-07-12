<?php

use Nubank\Authorizer\Application\Commands\CreateAccountCommand;
use Nubank\Authorizer\Domain\Entities\Account;
use Nubank\Authorizer\Infra\Repository\AccountRepository;
use PHPUnit\Framework\TestCase;

class CreateAccountCommandTest extends TestCase
{
    private AccountRepository $accountRepository;

    public function setUp(): void
    {
        $this->accountRepository = $this->createMock(AccountRepository::class);
    }

    public function testCreateAccount(): void
    {
        $command = new CreateAccountCommand($this->accountRepository);

        $this->accountRepository->expects($this->exactly(1))
            ->method('get')
            ->willReturn(null);

        $this->accountRepository->expects($this->exactly(1))
            ->method('create');

        $parameters = ['available-limit' => 10, 'active-card' => true];
        $result = $command->run($parameters);

        $this->assertEquals($parameters['available-limit'], $result->accountResponse->availableLimit);
        $this->assertEquals($parameters['active-card'], $result->accountResponse->activeCard);
        $this->assertEmpty($result->violations);
    }

    public function testCreateAlreadyCreatedAccount(): void
    {
        $command = new CreateAccountCommand($this->accountRepository);

        $this->accountRepository->expects($this->exactly(1))
            ->method('get')
            ->willReturn(new Account(limit: 100, activeCard: true));

        $result = $command->run(['available-limit' => 10, 'active-card' => true]);

        $this->assertEquals('account-already-initialized', $result->violations[0]);
    }
}
