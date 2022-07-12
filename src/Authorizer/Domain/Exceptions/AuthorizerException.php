<?php

namespace Nubank\Authorizer\Domain\Exceptions;

interface AuthorizerException
{
    function getViolations(): array;
}
