<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Throwable;
use Illuminate\Contracts\Container\Container;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException as IlluminateValidationException;
use Illuminate\Auth\AuthenticationException as IlluminateAuthenticationException;
use Api\Core\Response\Response;
use Api\Core\Exceptions\AbstractHttpException;
use Api\Exceptions\AccessDeniedException;
use Api\Exceptions\BadRequestException;
use Api\Exceptions\NotFoundException;
use Api\Exceptions\ValidationException;
use Api\Exceptions\AuthenticationException as CoreAuthenticationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Handler extends ExceptionHandler
{
    /**
     * @var Response
     */
    protected $response;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        \Illuminate\Auth\AuthenticationException::class,
        \Illuminate\Auth\Access\AuthorizationException::class,
        \Symfony\Component\HttpKernel\Exception\HttpException::class,
        \Illuminate\Database\Eloquent\ModelNotFoundException::class,
        \Illuminate\Session\TokenMismatchException::class,
        \Illuminate\Validation\ValidationException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Handler constructor.
     * @param Container $container
     * @param Response $response
     */
    public function __construct(
        Container $container,
        Response $response
    )
    {
        parent::__construct($container);

        $this->response = $response;
    }

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Determines if request is an api call.
     *
     * If the request URI contains '/api/v'.
     *
     * @param $request
     * @return bool
     */
    protected function isApiCall(Request $request)
    {
        return strpos($request->getUri(), '/api') !== false;
    }

    /**
     * @param Request $request
     * @param Throwable $exception
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws Throwable
     */
    public function render($request, Throwable $exception)
    {
        if (!$this->isApiCall($request)) {
            return parent::render($request, $exception);
        }

        if ($exception instanceof IlluminateValidationException) {
            return $this->response->exception(new ValidationException($exception->validator));
        }

        if ($exception instanceof UnauthorizedException) {
            return $this->response->exception(new AccessDeniedException($exception->getMessage()));
        }

        if ($exception instanceof NotFoundHttpException) {
            return $this->response->exception(new NotFoundException());
        }

        if ($exception instanceof ModelNotFoundException) {
            return $this->response->exception(new NotFoundException());
        }

        if ($exception instanceof IlluminateAuthenticationException) {
            return $this->response->exception(new CoreAuthenticationException());
        }

        if ($exception instanceof AbstractHttpException) {
            return $this->response->exception($exception);
        }

        return $this->response->exception(new BadRequestException($exception->getMessage()));
    }
}
