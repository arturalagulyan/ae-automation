<?php

namespace Renderer\Events;

/**
 * Class BaseEvent
 * @package Renderer\Events
 */
abstract class BaseEvent
{
    /**
     * @var array
     */
    private $data;

    /**
     * BaseEvent constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
