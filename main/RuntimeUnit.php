<?php

namespace Main;

use Core\Entity\Unit;
use PhpAmqpLib\Message\AMQPMessage;

class RuntimeUnit extends Unit
{
    protected string $name = 'test_orders';

    protected function run(AMQPMessage $msg): void
    {
        $this->logger->info('start -> ' . $msg->getBody());
        sleep(5);
        $this->logger->info('stop -> ' . $msg->getBody());
    }
}