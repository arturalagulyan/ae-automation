<?php

namespace Api\Exceptions;

use Api\Core\Exceptions\AbstractHttpException;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException as IlluminateValidationException;

/**
 * Class NotFoundException
 * @package Api\Core\Exceptions
 */
class ValidationException extends AbstractHttpException
{
    /**
     * @var IlluminateValidationException
     */
    protected $baseException;

    /**
     * ValidationException constructor.
     * @param $validator
     */
    public function __construct($validator)
    {
        $this->baseException = (new IlluminateValidationException($validator));

        $message = trans('errors.422');

        parent::__construct(new Response($message, 422, []));
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->baseException->errors();
    }
}
