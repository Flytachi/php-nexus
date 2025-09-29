<?php

namespace Core\Web;

use Flytachi\Kernel\Src\Errors\ClientError;
use Flytachi\Kernel\Src\Factory\Entity\Request;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\DeleteMapping;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\GetMapping;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\PostMapping;
use Flytachi\Kernel\Src\Factory\Mapping\Annotation\RequestMapping;
use Flytachi\Kernel\Src\Http\HttpCode;
use Flytachi\Kernel\Src\Stereotype\Response;
use Flytachi\Kernel\Src\Stereotype\ResponseJson;
use Flytachi\Kernel\Src\Stereotype\RestController;

#[RequestMapping('web/auth')]
class AuthController extends RestController
{
    public function __construct()
    {
        parent::__construct();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    #[GetMapping]
    public function sessionExist(): Response
    {
        return new Response([
            'session' => $_SESSION['NX_TOKEN'] ?? false
        ]);
    }

    #[PostMapping]
    public function sessionOpen(): Response
    {
        $haveUser = env('WEB_ADMIN_USER', '');
        $havePass = env('WEB_ADMIN_PASS', '');
        $request = Request::json();

        if (
            $haveUser == '' || $havePass == ''
            || !($request->username == $haveUser && $request->password == $havePass)
        ) {
            ClientError::throw('Uncorrected user or password.', HttpCode::BAD_REQUEST);
        }

        $_SESSION['NX_TOKEN'] = true;
        return new Response('ASK');
    }

    #[DeleteMapping]
    public function sessionClose(): Response
    {
        session_destroy();
        return new Response('ASK');
    }
}
