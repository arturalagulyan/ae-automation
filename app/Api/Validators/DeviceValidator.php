<?php

namespace Api\Validators;

/**
 * Class DeviceValidator
 * @package Api\Validators
 */
class DeviceValidator extends BaseApiValidator
{
    protected function validatePing()
    {
        $this
            ->addRule('dev_name', 'required');

//            ->addRule('dev_mac_id', 'required');
    }
}
