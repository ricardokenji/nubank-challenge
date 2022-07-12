<?php

namespace Nubank\Authorizer\Application\Response;

class AccountResponse
{
    public function __construct(public bool $activeCard, public int $availableLimit)
    {
    }
}
