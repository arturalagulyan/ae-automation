<?php

namespace Renderer\Steps;

/**
 * Class BaseStep
 * @package Renderer\Steps
 */
abstract class BaseStep
{
    /**
     * @var array
     */
    protected $data = [];

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return mixed
     */
    public abstract function process();
}
