<?php

namespace Mini\Framework\Exceptions\Ignition\FlareMiddleware;

use Mini\Framework\Exceptions\Ignition\Recorders\QueryRecorder\QueryRecorder;
use Spatie\FlareClient\Report;

class AddQueries
{
    protected QueryRecorder $queryRecorder;

    public function __construct()
    {
        $this->queryRecorder = app(QueryRecorder::class);
    }

    public function handle(Report $report, $next)
    {
        $report->group('queries', $this->queryRecorder->getQueries());

        return $next($report);
    }
}
