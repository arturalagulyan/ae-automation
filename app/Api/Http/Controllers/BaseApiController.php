<?php

namespace Api\Http\Controllers;

use Api\Core\Response\Response;
use Api\Services\BaseApiService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

/**
 * Class BaseApiController
 * @package Api\Http\Controllers
 */
abstract class BaseApiController extends Controller
{
    /**
     * @var BaseApiService
     */
    protected $service;

    /**
     * @var Response
     */
    protected $response;

    /**
     * BaseApiController constructor.
     * @param Response $response
     * @param BaseApiService $service
     */
    public function __construct(
        Response $response,
        BaseApiService $service
    )
    {
        $this->service = $service;
        $this->response = $response;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     * @throws \Exception
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function listable(Request $request)
    {
        return $this->response->make($this->service->listable($request->all()));
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     * @throws \Exception
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function single($id, Request $request)
    {
        return $this->response->make($this->service->single($id, $request->all()));
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function paginated(Request $request)
    {
        return $this->response->make($this->service->pagination($request->all()));
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function collected(Request $request)
    {
        return $this->response->make($this->service->collection($request->all()));
    }

    /**
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function create(Request $request)
    {
        return $this->response->make($this->service->create($request->all()));
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function update($id, Request $request)
    {
        return $this->response->make($this->service->update($id, $request->all()));
    }

    /**
     * @param $id
     * @param Request $request
     * @return mixed
     * @throws \Exception
     */
    public function replace($id, Request $request)
    {
        return $this->response->make($this->service->replace($id, $request->all()));
    }

    /**
     * @param $id
     * @return mixed
     * @throws \Exception
     */
    public function delete($id)
    {
        return $this->response->make($this->service->delete($id));
    }
}
