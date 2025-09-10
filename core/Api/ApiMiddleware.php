<?php

namespace Core\Api;

use Flytachi\Kernel\Src\Factory\Middleware\MiddlewareException;
use Flytachi\Kernel\Src\Http\Header;
use Flytachi\Kernel\Src\Http\HttpCode;
use Flytachi\Kernel\Src\Stereotype\Middleware;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class ApiMiddleware extends Middleware
{
    public function optionBefore(): void
    {
        if (Header::getHeader('Accept') !== 'application/json') {
            MiddlewareException::throw('Lock', HttpCode::LOCKED);
        }
        if (Header::getHeader('User-Agent') !== 'sentinel') {
            MiddlewareException::throw('Lock', HttpCode::LOCKED);
        }
    }
}
