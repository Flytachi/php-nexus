<?php

namespace Core\Web;

use Flytachi\Kernel\Src\Factory\Middleware\MiddlewareException;
use Flytachi\Kernel\Src\Stereotype\Middleware;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD)]
class WebMiddleware extends Middleware
{
    public function optionBefore(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (!(isset($_SESSION['NX_TOKEN']) && $_SESSION['NX_TOKEN'])) {
            throw new MiddlewareException('Authentication Failed');
        }
    }
}
