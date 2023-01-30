<?php

namespace Mini\Framework\Exceptions\Ignition\Support;

class LaravelVersion
{
    public static function major(): string
    {
        return explode('.', app()->version())[0];
    }
}
