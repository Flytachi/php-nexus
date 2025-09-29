<?php

namespace Core\Web;

use Core\Api\ApiLogController;
use Core\Api\ApiRemoteController;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\DeleteMapping;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\GetMapping;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\PatchMapping;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\RequestMapping;
use Flytachi\Kernel\Src\Stereotype\Response;
use Flytachi\Kernel\Src\Stereotype\ResponseJson;
use Flytachi\Kernel\Src\Stereotype\RestController;

#[WebMiddleware]
#[RequestMapping('web/api')]
class ApiController extends RestController
{
    #[GetMapping('service')]
    public function service(): ResponseJson
    {
        return (new ApiRemoteController)->status();
    }

    #[PatchMapping('service')]
    public function start(): Response
    {
        return (new ApiRemoteController)->start();
    }

    #[DeleteMapping('service')]
    public function stop(): Response
    {
        return (new ApiRemoteController)->stop();
    }

    #[GetMapping('logs/files')]
    public function logFiles(): ResponseJson
    {
        return (new ApiLogController)->files();
    }

    #[GetMapping('logs')]
    public function logList(): ResponseJson
    {
        return (new ApiLogController)->list();
    }


}
