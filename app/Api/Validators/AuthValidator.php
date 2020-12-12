<?php

namespace Api\Validators;

/**
 * Class AuthValidator
 * @package Api\Validators
 */
class AuthValidator extends BaseApiValidator
{
    /**
     *
     */
    protected function validateLogin()
    {
        $this
            ->addRule('email', 'required')
            ->addRule('email', 'string')
            ->addRule('email', 'email')

            ->addRule('password', 'required')
            ->addRule('password', 'string');
    }

    /**
     *
     */
    protected function validateRefresh()
    {
        $this
            ->addRule('refreshToken', 'required')
            ->addRule('refreshToken', 'string');
    }
}
