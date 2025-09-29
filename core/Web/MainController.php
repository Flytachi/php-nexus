<?php

namespace Core\Web;

use Flytachi\Kernel\Extra;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\GetMapping;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\RequestMapping;
use Flytachi\Kernel\Src\Stereotype\RestController;
use Flytachi\Kernel\Src\Stereotype\View;

#[RequestMapping('web')]
class MainController extends RestController
{
    #[GetMapping]
    public function main(): View
    {
        $info = Extra::projectInfo();
        return View::render('template', 'main', [
            'version' => $info['extra']['project']['version'] ?? '?',
            'name' => $info['extra']['project']['name'] ?? 'unknown',
        ]);
    }

    #[GetMapping('login')]
    public function login(): View
    {
        $info = Extra::projectInfo();
        return View::view('login', [
            'version' => $info['extra']['project']['version'] ?? '?',
            'name' => $info['extra']['project']['name'] ?? 'unknown',
        ]);
    }
}