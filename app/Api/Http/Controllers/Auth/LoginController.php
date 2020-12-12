<?php

namespace Api\Http\Controllers\Auth;

use Api\Core\Response\Response;
use Api\Http\Controllers\BaseApiController;
use Api\Services\AuthService;
use Illuminate\Http\Request;

/**
 * Class LoginController
 * @package Api\Http\Controllers\Auth
 */
class LoginController extends BaseApiController
{
    /**
     * LoginController constructor.
     * @param Response $response
     * @param AuthService $service
     */
    public function __construct(
        Response $response,
        AuthService $service
    )
    {
        parent::__construct($response, $service);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        return $this->response->make($this->service->login($request->all()));
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        return $this->response->make($this->service->logout());
    }
}
