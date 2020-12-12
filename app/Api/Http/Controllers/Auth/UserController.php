<?php

namespace Api\Http\Controllers\Auth;

use Api\Core\Response\Response;
use Api\Http\Controllers\BaseApiController;
use Api\Services\AuthService;

/**
 * Class UserController
 * @package Api\Http\Controllers\Auth
 */
class UserController extends BaseApiController
{
    /**
     * RefreshController constructor.
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
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        return $this->response->make($this->service->user());
    }
}
