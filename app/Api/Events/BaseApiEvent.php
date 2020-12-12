<?php

namespace Api\Events;

/**
 * Class BaseApiEvent
 * @package App\Events
 */
abstract class BaseApiEvent
{
    /**
     * @var array
     */
    public $data;

    /**
     * BaseEvent constructor.
     * @param array $data
     */
    public function __construct(array $data)
    {
        $this->data = $data;
    }
}
