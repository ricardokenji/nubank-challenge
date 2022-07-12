<?php

namespace Nubank\Authorizer\Application\Commands;

use Nubank\Authorizer\Application\Response\AccountResponse;
use Nubank\Authorizer\Application\Response\Response;
use Nubank\Authorizer\Application\Violations;
use Nubank\Authorizer\Domain\Entities\Transaction;
use Nubank\Authorizer\Domain\Exceptions\TransactionException;
use Nubank\Authorizer\Infra\Repository\AccountRepository;
use DateTime;
use Exception;

class CreateTransactionCommand implements Command
{
    public function __construct(private AccountRepository $accountRepository)
    {
    }

    /**
     * Validate and save transaction to an account
     * @param array $parameters
     * @return Response
     * @throws Exception
     */
    function run(array $parameters): Response
    {
        $violations = [];

        $account = $this->accountRepository->get();
        if (empty($account)) {
            $violations[] = Violations::AccountNotInitialized;
        } else {
            $transaction = $this->buildTransaction($parameters);

            try {
                $account->addTransaction($transaction);
            } catch (TransactionException $e) {
                $violations = array_merge($violations, $e->getViolations());
            }
        }

        return new Response(
            accountResponse: $account ? new AccountResponse(activeCard: $account->isActiveCard(), availableLimit: $account->getBalance()) : null,
            violations: $violations
        );
    }

    /**
     * Build transactions with parameters
     * @param array $parameters
     * @return Transaction
     * @throws Exception
     */
    private function buildTransaction(array $parameters): Transaction
    {
        return new Transaction(
            merchant: $parameters['merchant'],
            amount: $parameters['amount'],
            time: new DateTime($parameters['time'])
        );
    }
}
