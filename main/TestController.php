<?php

namespace Main;

use Flytachi\Kernel\Src\Factory\Mapping\Annotation\GetMapping;
use Flytachi\Kernel\Src\Stereotype\Response;
use Flytachi\Kernel\Src\Stereotype\RestController;

class TestController extends RestController
{
    #[GetMapping('api/test')]
    public function test(): Response
    {
        $tasks = [
            'Обработать заказ #1...',
            'Сгенерировать отчет по заказу #2.',
            'Отправить email для заказа #3....',
            'Архивировать заказ #4..',
            'Проверить платеж по заказу #5.',
            'Обновить статус заказа #6....',
        ];

        Pusher::push(...$tasks);
        return new Response("hello");
    }
}
