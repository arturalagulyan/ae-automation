<?php

namespace Api\Http\Controllers\Auth;

use Api\Core\Response\Response;
use Api\Http\Controllers\BaseApiController;
use Api\Services\AuthService;
use Illuminate\Http\Request;

/**
 * Class RefreshController
 * @package Api\Http\Controllers\Auth
 */
class RefreshController extends BaseApiController
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
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function refresh(Request $request)
    {
        return $this->response->make($this->service->refresh($request->all()));
    }
}
