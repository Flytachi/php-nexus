<?php

namespace Main;

use Flytachi\Kernel\Src\Factory\Mapping\Annotation\GetMapping;
use Flytachi\Kernel\Src\Stereotype\Response;
use Flytachi\Kernel\Src\Stereotype\RestController;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class MainController extends RestController
{
    #[GetMapping]
    public function hello(): Response
    {
        $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $channel = $connection->channel();

        $queueName = 'ut_test_orders';

        $channel->queue_declare($queueName, false, true, false, false);

        // Сообщения для отправки. Количество точек имитирует сложность задачи.
        $tasks = [
            'Обработать заказ #1...',
            'Сгенерировать отчет по заказу #2.',
            'Отправить email для заказа #3....',
            'Архивировать заказ #4..',
            'Проверить платеж по заказу #5.',
            'Обновить статус заказа #6....',
        ];

        foreach ($tasks as $task_body) {
            // Помечаем сообщение как 'persistent', чтобы оно пережило перезапуск RabbitMQ
            $msg = new AMQPMessage($task_body, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT]);
            $channel->basic_publish($msg, '', $queueName);
            echo " [x] Отправлен заказ: '$task_body'\n";
        }

        $channel->close();
        $connection->close();
        return new Response("hello");
    }
}
