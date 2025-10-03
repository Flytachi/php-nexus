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
        $tasks = [];
        for ($i = 1; $i <= 10; $i++) {
            $tasks[] = "Обработать заказ #".$i;
        }

        Pusher::push(...$tasks);
        return new Response("hello");
    }
}
