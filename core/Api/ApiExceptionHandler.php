<?php

namespace Core\Api;

use Flytachi\Kernel\Src\Factory\Error\ExceptionWrapper;

class ApiExceptionHandler extends ExceptionWrapper
{
    public static function getHeader(): array
    {
        return ['Content-Type' => 'application/json'];
    }

    public static function getBody(\Throwable $throwable): string
    {
        return self::constructJson($throwable);
    }
}