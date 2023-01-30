<?php

namespace Mini\Framework\Exceptions\Ignition\Exceptions;

use Spatie\Ignition\Contracts\Solution;
use Spatie\Ignition\Contracts\BaseSolution;
use Mini\Framework\Exceptions\HttpException;
use Spatie\Ignition\Contracts\ProvidesSolution;

class CannotExecuteSolutionForNonLocalIp extends HttpException implements ProvidesSolution
{
    public static function make(): self
    {
        return new self(403, 'Solutions cannot be run from your current IP address.');
    }

    public function getSolution(): Solution
    {
        return BaseSolution::create()
            ->setSolutionTitle('Checking your environment settings')
            ->setSolutionDescription("Solutions can only be executed by requests from a local IP address. Keep in mind that `APP_DEBUG` should set to false on any production environment.");
    }
}
