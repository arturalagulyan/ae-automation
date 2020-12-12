<?php

namespace Api\Transformers;

/**
 * Class AuthTransformer
 * @package Api\Transformers
 */
class AuthTransformer extends BaseApiTransformer
{
    /**
     * @param $content
     * @return array|mixed
     */
    public function transform($content)
    {
        return [
            'type' => 'Bearer',
            'token' => $content['access_token'],
            'expiresIn' => $content['expires_in'],
            'expiresAt' => $content['expires_in'] + time(),
            'refreshToken' => $content['refresh_token'],
            'refreshExpiresAt' => config('api.auth.rememberExpire') + time(),
        ];
    }
}
