<?php

namespace Mini\Framework\Routing;

use League\Route\Dispatcher as LeagueDispatcher;

class Dispatcher extends LeagueDispatcher
{
    use MiddlewareAwareTrait;
}
