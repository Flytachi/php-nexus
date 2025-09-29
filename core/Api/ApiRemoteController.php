<?php

namespace Core\Api;

use Core\Nexus;
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
                $response = Nexus::stats();
                break;
            case 'pids':
                $response = Nexus::threadList();
                break;
            case 'status':
                $status = Nexus::status();
                $response = [
                    'pid' => $status['pid'] ?? null,
                    'className' => $status['className'] ?? Nexus::class,
                    'condition' => $status['condition'] ?? 'passive',
                    'balancer' => $status['balancer'] ?? null,
                    'info' => $status['info'] ?? null,
                    'startedAt' => $status['startedAt'] ?? null
                ];
                break;
            case '':
                $status = Nexus::status();
                $response = [
                    'status' => [
                        'pid' => $status['pid'] ?? null,
                        'className' => $status['className'] ?? Nexus::class,
                        'condition' => $status['condition'] ?? 'passive',
                        'balancer' => $status['balancer'] ?? null,
                        'info' => $status['info'] ?? null,
                        'startedAt' => $status['startedAt'] ?? null
                    ],
                    'stats' => Nexus::stats(),
                    'pids' => Nexus::threadList()
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
        Nexus::dispatch();
        return new Response(null, HttpCode::ACCEPTED);
    }

    #[DeleteMapping]
    public function stop(): Response
    {
        Nexus::stop();
        return new Response(null, HttpCode::ACCEPTED);
    }
}
