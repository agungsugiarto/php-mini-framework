<?php

namespace Mini\Framework\Exceptions\Ignition\Support;

use Illuminate\Support\Str;

class LaravelVersion
{
    public static function major(): string
    {
        return Str::of(app()->version())->after('^')->before('.');
    }
}
