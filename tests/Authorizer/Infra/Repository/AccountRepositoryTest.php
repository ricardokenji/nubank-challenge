<?php

use Nubank\Authorizer\Domain\Entities\Account;
use Nubank\Authorizer\Infra\Repository\AccountRepository;
use PHPUnit\Framework\TestCase;

class AccountRepositoryTest extends TestCase
{
    public function testCreateAccount(): void {
        $accountRepository = new AccountRepository();

        $account = new Account(limit: 100, activeCard: true);

        $accountRepository->create($account);

        $this->assertEquals($account, $accountRepository->get());
    }

    public function testGetNotInitializedAccount(): void {
        $accountRepository = new AccountRepository();

        $account = $accountRepository->get();

        $this->assertNull($account);
    }
}
