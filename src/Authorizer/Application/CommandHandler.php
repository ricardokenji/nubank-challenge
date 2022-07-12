<?php

namespace Nubank\Authorizer\Application;

use ArrayObject;
use Nubank\Authorizer\Application\Commands\Command;
use Nubank\Authorizer\Application\Response\Response;

class CommandHandler
{
    private ArrayObject $commands;

    function __construct()
    {
        $this->commands = new ArrayObject();
    }

    /**
     * Routes the operation to the correct command
     * @param $operation
     * @param $parameters
     * @return Response
     */
    public function handle($operation, $parameters): Response
    {
        $command = $this->getOperationCommand($operation);
        return $command->run($parameters);
    }

    /**
     * Add a new command to the handler
     * @param $key
     * @param Command $command
     */
    public function addCommand($key, Command $command): void
    {
        $this->commands[$key] = $command;
    }

    /**
     * Get the correct command for the operation
     * @param $operation
     * @return Command
     */
    private function getOperationCommand($operation): Command
    {
        return $this->commands->offsetGet($operation);
    }
}
