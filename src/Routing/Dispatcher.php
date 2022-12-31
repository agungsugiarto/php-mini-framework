<?php

namespace Mini\Framework\Routing;

use League\Route\Dispatcher as LeagueDispatcher;
use Mini\Framework\Routing\MiddlewareAwareTrait;

class Dispatcher extends LeagueDispatcher
{
    use MiddlewareAwareTrait;
}