<?php

namespace Mini\Framework\Exceptions\Ignition\Http\Middleware;

use Closure;
use Mini\Framework\Exceptions\Ignition\Support\RunnableSolutionsGuard;

class RunnableSolutionsEnabled
{
    public function handle($request, Closure $next)
    {
        if (! RunnableSolutionsGuard::check()) {
            abort(404);
        }

        return $next($request);
    }
}
