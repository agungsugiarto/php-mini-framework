<?php

namespace Mini\Framework\Exceptions\Ignition\ContextProviders;

use Psr\Http\Message\ServerRequestInterface;
use Spatie\FlareClient\Context\ContextProvider;
use Spatie\FlareClient\Context\ContextProviderDetector;

class LaravelContextProviderDetector implements ContextProviderDetector
{
    public function detectCurrentContext(): ContextProvider
    {
        if (app()->runningInConsole()) {
            return new LaravelConsoleContextProvider($_SERVER['argv'] ?? []);
        }

        $request = app(ServerRequestInterface::class);

        return new LaravelRequestContextProvider($request);
    }
}
