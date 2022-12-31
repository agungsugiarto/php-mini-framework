<?php

namespace Mini\Framework\Routing;

use Mini\Framework\Routing\Route;
use Mini\Framework\Routing\Dispatcher;
use Psr\Http\Message\ResponseInterface;
use League\Route\Router as LeagueRouter;
use Psr\Http\Message\ServerRequestInterface;
use Mini\Framework\Routing\MiddlewareAwareTrait;

class Router extends LeagueRouter
{
    use MiddlewareAwareTrait;

    /**
     * {@inheritdoc}
     */
    public function map(string $method, string $path, $handler): Route
    {
        $path  = sprintf('/%s', ltrim($path, '/'));
        $route = new Route($method, $path, $handler);

        $this->routes[] = $route;

        return $route;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        if (false === $this->routesPrepared) {
            $this->prepareRoutes($request);
        }

        /** @var Dispatcher $dispatcher */
        $dispatcher = (new Dispatcher($this->routesData))->setStrategy($this->getStrategy());

        foreach ($this->getMiddlewareStack() as $middleware) {
            if (is_string($middleware)) {
                $dispatcher->lazyMiddleware($middleware);
                continue;
            }

            $dispatcher->middleware($middleware);
        }

        return $dispatcher->dispatchRequest($request);
    }
}
