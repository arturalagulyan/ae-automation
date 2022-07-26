<?php

namespace Api\Http\Controllers;

use Api\Core\Response\Response;
use Api\Services\UploadService;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;

/**
 * Class UploadController
 * @package Api\Http\Controllers
 */
class UploadController extends BaseApiController
{
    /**
     * UploadController constructor.
     * @param Response $response
     * @param UploadService $service
     */
    public function __construct(
        Response $response,
        UploadService $service
    )
    {
        parent::__construct($response, $service);
    }

    /**
     * @param Request $request
     * @return IlluminateResponse
     */
    public function upload(Request $request): IlluminateResponse
    {
        return $this->response->make($this->service->upload($request->all()));
    }
}
