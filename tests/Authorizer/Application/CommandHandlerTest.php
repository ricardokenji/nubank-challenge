<?php

use Nubank\Authorizer\Application\CommandHandler;
use Nubank\Authorizer\Application\Commands\Command;
use PHPUnit\Framework\TestCase;

class CommandHandlerTest extends TestCase
{
    public function testHandleCommand(): void
    {
        $commandHandler = new CommandHandler();
        $command = $this->createMock(Command::class);

        $operation = 'test';
        $commandHandler->addCommand($operation, $command);

        $command->expects($this->exactly(1))
            ->method('run');

        $commandHandler->handle($operation, []);
    }
}
