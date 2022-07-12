<?php
require 'App.php';

use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function testInitialization()
    {
        $input = '{"account": {"active-card": true, "available-limit": 175}} 
                    {"account": {"active-card": true, "available-limit": 350}}';

        $app = new App();
        $result = $app->init($input);

        $this->assertStringContainsString('{"accountResponse":{"activeCard":true,"availableLimit":175},"violations":[]}', $result);
        $this->assertStringContainsString('{"accountResponse":{"activeCard":true,"availableLimit":175},"violations":["account-already-initialized"]}', $result);
    }
}
