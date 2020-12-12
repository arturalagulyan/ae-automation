<?php

namespace Api\Services;

use Api\Filters\UserFilter;
use Api\Repositories\UserRepository;
use Api\Services\Traits\OAuthProxy;
use Api\Services\Traits\ThrottlesLogins;
use Api\Transformers\AuthTransformer;
use Api\Transformers\AuthUserTransformer;
use Api\Validators\AuthValidator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class AuthService
 * @package Api\Services
 */
class AuthService extends BaseApiService
{
    use ThrottlesLogins, OAuthProxy;

    /**
     * @var array
     */
    protected $authSelects = [
        'name',
        'email',
    ];

    /**
     * AuthService constructor.
     * @param UserFilter $filter
     * @param AuthValidator $validator
     * @param UserRepository $repository
     * @param AuthTransformer $transformer
     */
    public function __construct(
        UserFilter $filter,
        AuthValidator $validator,
        UserRepository $repository,
        AuthTransformer $transformer
    )
    {
        parent::__construct($filter, $validator, $repository, $transformer);
    }

    /**
     * @return array|mixed
     * @throws \Exception
     * @throws \Prettus\Repository\Exceptions\RepositoryException
     */
    public function user()
    {
        $this->setTransformer(AuthUserTransformer::class);

        return $this->single(Auth::id());
    }

    /**
     * @param array $data
     * @return array|mixed
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function login(array $data)
    {
        $this->validator
            ->setData($data)
            ->validate('login');

        if ($this->hasTooManyLoginAttempts($data)) {
            $this->fireLockoutEvent();

            $this->sendLockoutResponse($data);
        }

        $response = $this->attemptLogin($data);

        if ($response) {
            return $this->transformer->transform($response);
        }

        $this->incrementLoginAttempts($data);

        $this->sendFailedLoginResponse();
    }

    /**
     * @param array $data
     * @return array|mixed
     * @throws ValidationException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function refresh(array $data)
    {
        $this->validator
            ->setData($data)
            ->validate('refresh');

        $response = $this->attemptRefresh($data);

        if ($response) {
            return $this->transformer->transform($response);
        }

        $this->sendFailedRefreshResponse();
    }

    /**
     * @return bool
     */
    public function logout()
    {
        $this->guard()->user()->token()->revoke();

        return true;
    }

    /**
     * @return mixed
     */
    protected function guard()
    {
        return Auth::guard('api');
    }

    /**
     * @param array $data
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    protected function attemptLogin(array $data)
    {
        if (!$this->attemptOriginal($data)) {
            return false;
        }

        try {
            return $this->proxy('password', [
                'username' => $data['email'],
                'password' => $data['password'],
            ]);
        } catch (HttpException $e) {
            return false;
        }
    }

    /**
     * @param array $data
     * @return bool|mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function attemptRefresh(array $data)
    {
        try {
            return $this->proxy('refresh_token', [
                'refresh_token' => $data['refreshToken']
            ]);
        } catch (HttpException $exception) {
            return false;
        }
    }

    /**
     * @param array $data
     * @return bool
     */
    protected function attemptOriginal(array $data)
    {
        return Auth::guard()->attempt([
            'email' => $data['email'],
            'password' => $data['password']
        ]);
    }

    /**
     * @throws ValidationException
     */
    protected function sendFailedLoginResponse()
    {
        throw ValidationException::withMessages([
            'email' => ['Invalid credentials!'],
        ]);
    }

    /**
     * @throws ValidationException
     */
    protected function sendFailedRefreshResponse()
    {
        throw ValidationException::withMessages([
            'refreshToken' => ['Refresh Failed']
        ]);
    }
}
