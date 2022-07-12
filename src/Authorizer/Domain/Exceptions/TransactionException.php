<?php

namespace Nubank\Authorizer\Domain\Exceptions;

use RuntimeException;

class TransactionException extends RuntimeException implements AuthorizerException
{
    public function __construct(private array $violations)
    {
        parent::__construct(implode(',', $violations));
    }

    public function getViolations(): array
    {
        return $this->violations;
    }
}
