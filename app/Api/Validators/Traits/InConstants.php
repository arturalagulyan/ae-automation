<?php

namespace Api\Validators\Traits;

/**
 * Trait InConstants
 * @package Api\Validators\Traits
 */
trait InConstants
{
    /**
     * @param $class
     * @return string
     * @throws \Exception
     */
    protected function constant($class)
    {
        return get_class_constants($class);
    }
}
