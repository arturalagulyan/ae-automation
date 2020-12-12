<?php

namespace Api\Http\Middleware;

use Closure;

/**
 * Class BeforeMiddleware
 * @package Api\Http\Middleware
 */
class BeforeMiddleware
{
    /**
     * @param $request
     * @param Closure $next
     * @return Closure
     */
    public function handle($request, Closure $next)
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, DELETE, PUT, PATCH, OPTIONS');
        header("Access-Control-Allow-Headers: Origin, X-Requested-With, Cache-Control, Content-Type, Accept, Access-Control-Request-Method, Authorization, Accept-Language, The-Timezone-IANA, Locale");
        header('Access-Control-Allow-Credentials: true');

        return $next($request);
    }
}
