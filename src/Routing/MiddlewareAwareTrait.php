<?php

namespace Mini\Framework\Routing;

use Illuminate\Container\Container;
use InvalidArgumentException;
use League\Route\Middleware\MiddlewareAwareTrait as LeagueMiddlewareAwareTrait;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

trait MiddlewareAwareTrait
{
    use LeagueMiddlewareAwareTrait;

    /**
     * {@inheritdoc}
     */
    protected function resolveMiddleware($middleware, ?ContainerInterface $container = null): MiddlewareInterface
    {
        if ($container === null && is_string($middleware) && class_exists($middleware)) {
            $middleware = new $middleware();
        }

        if ($container !== null && is_string($middleware) && $container instanceof Container) {
            $middleware = $container->make($middleware);
        }

        if ($middleware instanceof MiddlewareInterface) {
            return $middleware;
        }

        throw new InvalidArgumentException(sprintf('Could not resolve middleware class: %s', $middleware));
    }
}
