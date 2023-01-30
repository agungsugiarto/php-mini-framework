<?php

namespace Mini\Framework\Exceptions\Ignition\FlareMiddleware;

use Closure;
use Mini\Framework\Exceptions\Ignition\Recorders\DumpRecorder\DumpRecorder;
use Spatie\FlareClient\FlareMiddleware\FlareMiddleware;
use Spatie\FlareClient\Report;

class AddDumps implements FlareMiddleware
{
    protected DumpRecorder $dumpRecorder;

    public function __construct()
    {
        $this->dumpRecorder = app(DumpRecorder::class);
    }

    public function handle(Report $report, Closure $next)
    {
        $report->group('dumps', $this->dumpRecorder->getDumps());

        return $next($report);
    }
}
