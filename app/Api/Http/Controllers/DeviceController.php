<?php

namespace Api\Http\Controllers;

use Api\Core\Response\Response;
use Api\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;

/**
 * Class DeviceController
 * @package Api\Http\Controllers
 */
class DeviceController extends BaseApiController
{
    /**
     * DeviceController constructor.
     * @param Response $response
     * @param DeviceService $service
     */
    public function __construct(
        Response $response,
        DeviceService $service
    )
    {
        parent::__construct($response, $service);
    }

    /**
     * @param Request $request
     * @return IlluminateResponse
     */
    public function ping(Request $request): IlluminateResponse
    {
        return $this->response->make($this->service->ping($request->all()));
    }
}
