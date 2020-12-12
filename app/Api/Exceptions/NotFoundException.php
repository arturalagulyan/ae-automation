<?php

namespace Api\Exceptions;

use Api\Core\Exceptions\AbstractHttpException;
use Illuminate\Http\Response;

/**
 * Class NotFoundException
 * @package Api\Core\Exceptions
 */
class NotFoundException extends AbstractHttpException
{
    /**
     * NotFoundException constructor.
     * @param string $message
     */
    public function __construct($message = '')
    {
        if (empty($message)) {
            $message = trans('errors.404');
        }

        parent::__construct(new Response($message, 404, []));
    }
}
