<?php

namespace Api\Core\Exceptions;

use Illuminate\Http\Response;

/**
 * Class AbstractHttpException
 * @package Api\Core\Exceptions
 */
abstract class AbstractHttpException extends \RuntimeException
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * AbstractHttpException constructor.
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return [];
    }
}