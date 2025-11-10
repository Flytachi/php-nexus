<?php

namespace Core\Api;

use Flytachi\Kernel\Extra;
use Flytachi\Kernel\Src\Errors\ClientError;
use Flytachi\Kernel\Src\Factory\Entity\Request;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\GetMapping;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\RequestMapping;
use Flytachi\Kernel\Src\Http\HttpCode;
use Flytachi\Kernel\Src\Stereotype\ResponseJson;
use Flytachi\Kernel\Src\Stereotype\RestController;

#[ApiMiddleware]
#[RequestMapping('api/logs')]
class ApiLogController extends RestController
{
    #[GetMapping('files')]
    public function files(): ResponseJson
    {
        $files = glob(Extra::$pathStorageLog . '/*.log');
        foreach ($files as $key => $file) {
            $files[$key] = basename($file, '.log');
        }
        $files = array_reverse($files);
        return new ResponseJson($files);
    }

    #[GetMapping]
    public function list(): ResponseJson
    {
        $request = Request::params(false);
        $limit = $request->limit ?? 1000;
        if (!is_numeric($limit)) {
            ClientError::throw('limit must be numeric', HttpCode::BAD_REQUEST);
        }
        $limit = (int) $limit;
        $logFile = Extra::$pathStorageLog . '/' . ($request->filename ?? '') . '.log';

        $logs = [];
        if (file_exists($logFile)) {
            $lines = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $lastLines = array_slice($lines, -$limit);
            foreach ($lastLines as $line) {
                preg_match('/\b(DEBUG|INFO|WARNING|NOTICE|ERROR|CRITICAL|ALERT)\b/', $line, $matches);
                $logLevel = isset($matches[1]) ? strtolower($matches[1]) : "default";

                $logs[] = [
                    'level' => $logLevel,
                    'message' => $line
                ];
            }
        }

        return new ResponseJson($logs);
    }
}