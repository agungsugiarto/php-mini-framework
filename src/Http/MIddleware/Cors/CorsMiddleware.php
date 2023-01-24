<?php

namespace Mini\Framework\Http\Middleware\Cors;

use Closure;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CorsMiddleware
{
    /** @var CorsService */
    protected $cors;

    public function __construct(CorsService $cors)
    {
        $this->cors = $cors;
    }

    public function handle(ServerRequestInterface $request, Closure $next)
    {
        // // Check if we're dealing with CORS and if we should handle it
        // if (! $this->shouldRun($request)) {
        //     return $next($request);
        // }

        // For Preflight, return the Preflight response
        if ($this->cors->isPreflightRequest($request)) {
            $response = $this->cors->handlePreflightRequest($request);
            $response = $this->cors->varyHeader($response, 'Access-Control-Request-Method');

            return $response;
        }

        /** @var ResponseInterface Handle the request */
        $response = $next($request);

        if ($request->getMethod() === 'OPTIONS') {
            $response = $this->cors->varyHeader($response, 'Access-Control-Request-Method');
        }

        return $this->addHeaders($request, $response);
    }

    /**
     * Add the headers to the Response, if they don't exist yet.
     */
    protected function addHeaders(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        if (! $response->hasHeader('Access-Control-Allow-Origin')) {
            // Add the CORS headers to the Response
            $response = $this->cors->addActualRequestHeaders($response, $request);
        }

        return $response;
    }

    // /**
    //  * Determine if the request has a URI that should pass through the CORS flow.
    //  *
    //  * @return bool
    //  */
    // protected function shouldRun(ServerRequestInterface $request): bool
    // {
    //     return $this->isMatchingPath($request);
    // }

    // /**
    //  * The the path from the config, to see if the CORS Service should run
    //  *
    //  * @return bool
    //  */
    // protected function isMatchingPath(ServerRequestInterface $request): bool
    // {
    //     // Get the paths from the config or the middleware
    //     $paths = $this->getPathsByHost($request->getUri()->getHost());

    //     foreach ($paths as $path) {
    //         if ($path !== '/') {
    //             $path = trim($path, '/');
    //         }

    //         if ($request->fullUrlIs($path) || $request->is($path)) {
    //             return true;
    //         }
    //     }

    //     return false;
    // }

    // /**
    //  * Paths by given host or string values in config by default
    //  *
    //  * @param string $host
    //  * @return array
    //  */
    // protected function getPathsByHost(string $host)
    // {
    //     $paths = config('cors.paths', []);
    //     // If where are paths by given host
    //     if (isset($paths[$host])) {
    //         return $paths[$host];
    //     }
    //     // Defaults
    //     return array_filter($paths, function ($path) {
    //         return is_string($path);
    //     });
    // }
}
