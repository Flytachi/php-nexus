<?php

namespace Core\Api;

use Core\Reactor;
use Flytachi\Kernel\Src\Errors\ClientError;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\DeleteMapping;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\GetMapping;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\PatchMapping;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\RequestMapping;
use Flytachi\Kernel\Src\Http\HttpCode;
use Flytachi\Kernel\Src\Stereotype\Response;
use Flytachi\Kernel\Src\Stereotype\ResponseJson;
use Flytachi\Kernel\Src\Stereotype\RestController;

#[ApiMiddleware]
#[RequestMapping('api/remote')]
class ApiRemoteController extends RestController
{
    #[GetMapping]
    #[GetMapping('{argument}')]
    public function status(string $argument = ''): ResponseJson
    {
        switch ($argument) {
            case 'stats':
                $response = Reactor::stats();
                break;
            case 'pids':
                $response = Reactor::threadList();
                break;
            case 'status':
                $status = Reactor::status();
                $response = [
                    'pid' => $status['pid'] ?? null,
                    'className' => $status['className'] ?? Reactor::class,
                    'condition' => $status['condition'] ?? 'passive',
                    'balancer' => $status['balancer'] ?? null,
                    'info' => $status['info'] ?? null,
                    'startedAt' => $status['startedAt'] ?? null
                ];
                break;
            case '':
                $status = Reactor::status();
                $response = [
                    'status' => [
                        'pid' => $status['pid'] ?? null,
                        'className' => $status['className'] ?? Reactor::class,
                        'condition' => $status['condition'] ?? 'passive',
                        'balancer' => $status['balancer'] ?? null,
                        'info' => $status['info'] ?? null,
                        'startedAt' => $status['startedAt'] ?? null
                    ],
                    'stats' => Reactor::stats()
                ];
                break;
            default:
                $url = parseUrlDetail($_SERVER['REQUEST_URI'])['path'] ?? '';
                ClientError::throw("GET '{$url}' url not found", HttpCode::NOT_FOUND);
        }
        return new ResponseJson($response);
    }

    #[PatchMapping]
    public function start(): Response
    {
        Reactor::dispatch();
        return new Response(null, HttpCode::ACCEPTED);
    }

    #[DeleteMapping]
    public function stop(): Response
    {
        Reactor::stop();
        return new Response(null, HttpCode::ACCEPTED);
    }
}
