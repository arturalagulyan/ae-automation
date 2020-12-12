<?php

namespace Api\Exceptions;

use Api\Core\Exceptions\AbstractHttpException;
use Illuminate\Http\Response;

/**
 * Class AuthenticationException
 * @package Api\Core\Exceptions
 */
class AuthenticationException extends AbstractHttpException
{
    /**
     * AuthenticationException constructor.
     */
    public function __construct()
    {
        $message = trans('errors.401');

        parent::__construct(new Response($message, 401, []));
    }
}
