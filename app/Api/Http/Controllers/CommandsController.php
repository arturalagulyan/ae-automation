<?php

namespace Api\Http\Controllers;

use Api\Core\Response\Response;
use Api\Services\CommandsService;
use Illuminate\Http\Request;
use Illuminate\Http\Response as IlluminateResponse;
use Illuminate\Routing\Controller;
use Illuminate\Validation\ValidationException;

/**
 * Class CommandsController
 * @package Api\Http\Controllers
 */
class CommandsController extends Controller
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * @var CommandsService
     */
    protected $service;

    /**
     * CommandsController constructor.
     * @param Response $response
     * @param CommandsService $service
     */
    public function __construct(
        Response $response,
        CommandsService $service
    )
    {
        $this->service = $service;
        $this->response = $response;
    }

    /**
     * @param Request $request
     * @return IlluminateResponse
     * @throws ValidationException
     */
    public function command(Request $request): IlluminateResponse
    {
        return $this->response->make($this->service->command($request->all()));
    }
}
