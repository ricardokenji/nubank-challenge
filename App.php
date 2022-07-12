<?php

use Nubank\Authorizer\Application\CommandHandler;
use Nubank\Authorizer\Application\Commands\CreateAccountCommand;
use Nubank\Authorizer\Application\Commands\CreateTransactionCommand;
use Nubank\Authorizer\Infra\Repository\AccountRepository;

class App
{
    /**
     * Run application initialization and execute command
     */
    public function init($operations): string
    {
        ob_start();
        $commandHandler = $this->initializeCommandHandlerDependencies();
        $lines = explode(PHP_EOL, $operations);

        foreach ($lines as $line) {
            if (empty($line)) {
                continue;
            }
            $request = json_decode($line, true);
            $operation = $this->getOperation($request);
            $parameters = $this->getParameters($request);
            echo json_encode($commandHandler->handle(operation: $operation, parameters: $parameters)), PHP_EOL;
        }
        $response = ob_get_contents();
        ob_end_clean();
        return $response;
    }

    /**
     * Get operation name from request
     * @param array $request
     * @return string
     */
    private function getOperation(array $request): string
    {
        return array_key_first($request);
    }

    /**
     * Get parameters from request
     * @param array $request
     * @return array
     */
    private function getParameters(array $request): array
    {
        return array_shift($request);
    }

    /**
     * Initialize the handler with required dependencies
     * @return CommandHandler
     */
    private function initializeCommandHandlerDependencies(): CommandHandler
    {
        $accountRepository = new AccountRepository();

        $handler = new CommandHandler();
        $handler->addCommand('account', new CreateAccountCommand($accountRepository));
        $handler->addCommand("transaction", new CreateTransactionCommand($accountRepository));

        return $handler;
    }
}

