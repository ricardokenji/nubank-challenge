<?php

namespace Nubank\Authorizer\Application\Response;

class Response
{
    public function __construct(public AccountResponse|null $accountResponse, public array $violations = [])
    {
    }
}
