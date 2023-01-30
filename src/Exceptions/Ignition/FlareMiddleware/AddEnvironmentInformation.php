<?php

namespace Mini\Framework\Exceptions\Ignition\FlareMiddleware;

use Closure;
use Spatie\FlareClient\FlareMiddleware\FlareMiddleware;
use Spatie\FlareClient\Report;

class AddEnvironmentInformation implements FlareMiddleware
{
    public function handle(Report $report, Closure $next)
    {
        $report->frameworkVersion(app()->version());

        $report->group('env', [
            'mini_version' => app()->version(),
            'mini_locale' => app()->getLocale(),
            'app_debug' => config('app.debug'),
            'app_env' => config('app.env'),
            'php_version' => phpversion(),
        ]);

        return $next($report);
    }
}
