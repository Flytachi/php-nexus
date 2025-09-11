<?php

namespace Main;

use Flytachi\Kernel\Src\Unit\Algorithm;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class Pusher
{
    private static string $name = 'tests';

    private static function connection(): AMQPStreamConnection
    {
        return new AMQPStreamConnection(
            env('AMQP_HOST', 'localhost'),
            env('AMQP_PORT', 5672),
            env('AMQP_USER', 'guest'),
            env('AMQP_PASS', 'guest'),
        );
    }

    public static function push(mixed ...$messages): void
    {
        $connection = self::connection();
        $channel = $connection->channel();
        $queueName = 'ut_' . (
            empty(self::$name)
                ? Algorithm::random(7)
                : self::$name
            );

        $channel->queue_declare($queueName, false, true, false, false);

        if (empty($messages)) return;

        foreach ($messages as $message) {
            $msg = new AMQPMessage($message, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
            $channel->basic_publish($msg, '', $queueName);
        }

        $channel->close();
        $connection->close();
    }
}