<?php

namespace Api\Services\Traits;

use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

/**
 * Trait OAuthProxy
 * @package Api\Services\Traits
 */
trait OAuthProxy
{
    /**
     * @param $grantType
     * @param array $data
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function proxy($grantType, array $data = [])
    {
        $data = array_merge($data, [
            'client_id' => config('auth.passport.password.id'),
            'client_secret' => config('auth.passport.password.secret'),
            'grant_type' => $grantType,
            'scope' => '*'
        ]);

        $http = new \GuzzleHttp\Client([
            'verify' => false
        ]);

        try {
            $response = $http->post(request()->getSchemeAndHttpHost() . '/oauth/token', [
                'form_params' => $data
            ]);
        } catch (RequestException $e) {
            if ($e->getResponse()->getStatusCode() === 401) {
                throw new UnauthorizedHttpException('unauthorized');
            }

            throw new BadRequestHttpException();
        }

        return json_decode($response->getBody(), true);
    }

}
