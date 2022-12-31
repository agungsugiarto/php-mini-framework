<?php

namespace Mini\Framework\Routing;

use Illuminate\Container\Container;
use League\Route\Route as LeagueRoute;
use Psr\Container\ContainerInterface;

class Route extends LeagueRoute
{
    use MiddlewareAwareTrait;

    /**
     * {@inheritdoc}
     */
    protected function resolve(string $class, ?ContainerInterface $container = null)
    {
        if ($container instanceof Container) {
            return $container->make($class);
        }

        if (class_exists($class)) {
            return new $class();
        }

        return $class;
    }
}
