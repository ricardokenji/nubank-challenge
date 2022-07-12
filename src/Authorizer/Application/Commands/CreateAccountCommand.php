<?php

namespace Nubank\Authorizer\Application\Commands;

use Nubank\Authorizer\Application\Exceptions\AccountAlreadyInitializedException;
use Nubank\Authorizer\Application\Exceptions\TransactionException;
use Nubank\Authorizer\Application\Response\AccountResponse;
use Nubank\Authorizer\Application\Response\Response;
use Nubank\Authorizer\Application\Violations;
use Nubank\Authorizer\Domain\Entities\Account;
use Nubank\Authorizer\Infra\Repository\AccountRepository;

class CreateAccountCommand implements Command
{
    public function __construct(private AccountRepository $accountRepository)
    {
    }

    /**
     * Validate and create account
     * @param array $parameters
     * @return Response
     */
    function run(array $parameters): Response
    {
        $violations = [];

        $account = $this->accountRepository->get();

        if (!empty($account)) {
            $violations[] = Violations::AccountAlreadyInitialized;
        } else {
            $account = new Account(limit: $parameters['available-limit'], activeCard: $parameters['active-card']);
            $this->accountRepository->create($account);
        }

        return new Response(
            accountResponse: new AccountResponse(activeCard: $account->isActiveCard(), availableLimit: $account->getBalance()),
            violations: $violations
        );
    }
}
