<?php

namespace Nubank\Authorizer\Application\Commands;

use Nubank\Authorizer\Application\Response\Response;

interface Command
{
    function run(array $parameters): Response;
}
