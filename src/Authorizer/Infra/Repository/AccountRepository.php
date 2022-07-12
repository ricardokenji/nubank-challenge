<?php

namespace Nubank\Authorizer\Infra\Repository;

use Nubank\Authorizer\Domain\Entities\Account;

class AccountRepository
{
    private Account $account;

    /**
     * Save account to memory
     * @param Account $account
     */
    public function create(Account $account): void
    {
        $this->account = $account;
    }

    /**
     * Get saved account or return null if no account is available
     * @return Account|null
     */
    public function get(): Account|null
    {
        if (empty($this->account)) {
            return null;
        }
        return $this->account;
    }
}
