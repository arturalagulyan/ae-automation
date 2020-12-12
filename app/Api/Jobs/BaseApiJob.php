<?php

namespace Api\Jobs;

/**
 * Class BaseApiJob
 * @package Api\Jobs
 */
abstract class BaseApiJob
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var mixed
     */
    protected $response;

    /**
     * BaseApiJob constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }
}
