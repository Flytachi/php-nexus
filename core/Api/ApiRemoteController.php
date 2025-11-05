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
use Flytachi\Kernel\Src\Thread\Entity\ProcessCondition;

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
                $status = Nexus::status()?->status;
                $response = [
                    'pid' => $status->pid ?? null,
                    'className' => $status->className ?? Nexus::class,
                    'condition' => $status->condition?->name ?? ProcessCondition::PASSIVE->name,
                    'balancer' => $status->balancer ?? null,
                    'info' => $status->info ?? null,
                    'startedAt' => $status?->getStartedAt()
                ];
                break;
            case 'info':
                $info = Nexus::status(true);
                $response = [
                    'status' => [
                        'pid' => $info?->status->pid ?? null,
                        'className' => $info?->status->className ?? Nexus::class,
                        'condition' => $info?->status->condition->name ?? ProcessCondition::PASSIVE->name,
                        'balancer' => $info?->status->balancer ?? null,
                        'info' => $info?->status->info ?? null,
                        'startedAt' => $info?->status->getStartedAt()
                    ],
                    'stats' => $info->stats ?? null,
                ];
                break;
            case '':
                $info = Nexus::status(true);
                $response = [
                    'info' => [
                        'status' => [
                            'pid' => $info?->status->pid ?? null,
                            'className' => $info?->status->className ?? Nexus::class,
                            'condition' => $info?->status->condition->name ?? ProcessCondition::PASSIVE->name,
                            'balancer' => $info?->status->balancer ?? null,
                            'info' => $info?->status->info ?? null,
                            'startedAt' => $info?->status->getStartedAt()
                        ],
                        'stats' => $info->stats ?? null,
                    ],
                    'stats' => Nexus::stats(),
                    'units' => Nexus::threadList()
                ];
                break;
            default:
                $url = parseUrlDetail($_SERVER['REQUEST_URI'])['path'] ?? '';
                ClientError::throw("GET '{$url}' url not found", HttpCode::NOT_FOUND);
        }
        return new ResponseJson($response);
    }

    #[GetMapping('units')]
    public function units(): ResponseJson
    {
        $threads = Nexus::threadListInfo(true);
        foreach ($threads as $key => $thread) {
            $threadData = [
                'status' => (array) $thread->status,
                'stats' => $thread->stats ?? null,
            ];
            $threadData['status']['condition'] = $threadData['status']['condition']->name;
            $threadData['status']['startedAt'] = date('Y-m-d H:i:s P', $threadData['status']['startedAt']);
            $threads[$key] = $threadData;
        }
        return new ResponseJson($threads);
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
