<?php

namespace Api\Validators;

/**
 * Class CommandsValidator
 * @package Api\Validators
 */
class CommandsValidator extends BaseApiValidator
{
    /**
     *
     */
    protected function validateCommand()
    {
        $this
            ->addRule('action', 'required')
            ->addRule('action', 'in', ['server_status']);
    }
}
