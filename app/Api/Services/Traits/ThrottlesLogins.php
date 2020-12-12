<?php

namespace Api\Services\Traits;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

/**
 * Trait ThrottlesLogins
 * @package Api\Services\Traits
 */
trait ThrottlesLogins
{
    /**
     * @var int
     */
    protected $maxAttempts = 5;

    /**
     * @var int
     */
    protected $decayMinutes = 1;

    /**
     * @param array $data
     * @return bool
     */
    protected function hasTooManyLoginAttempts(array $data)
    {
        return $this->limiter()->tooManyAttempts(
            $this->throttleKey($data), $this->maxAttempts()
        );
    }

    /**
     * @param array $data
     */
    protected function incrementLoginAttempts(array $data)
    {
        $this->limiter()->hit(
            $this->throttleKey($data), $this->decayMinutes() * 60
        );
    }

    /**
     * @param array $data
     * @throws ValidationException
     */
    protected function sendLockoutResponse(array $data)
    {
        $seconds = $this->limiter()->availableIn(
            $this->throttleKey($data)
        );

        throw ValidationException::withMessages([
            'email' => [Lang::get('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ])],
        ])->status(Response::HTTP_TOO_MANY_REQUESTS);
    }

    /**
     * @param array $data
     */
    protected function clearLoginAttempts(array $data)
    {
        $this->limiter()->clear($this->throttleKey($data));
    }

    /**
     *
     */
    protected function fireLockoutEvent()
    {
        event(new Lockout(request()));
    }

    /**
     * @param array $data
     * @return string
     */
    protected function throttleKey(array $data)
    {
        return Str::lower($data['email']).'|'.request()->ip();
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|mixed
     */
    protected function limiter()
    {
        return app(RateLimiter::class);
    }

    /**
     * @return int
     */
    public function maxAttempts()
    {
        return property_exists($this, 'maxAttempts') ? $this->maxAttempts : 5;
    }

    /**
     * @return int
     */
    public function decayMinutes()
    {
        return property_exists($this, 'decayMinutes') ? $this->decayMinutes : 1;
    }
}
