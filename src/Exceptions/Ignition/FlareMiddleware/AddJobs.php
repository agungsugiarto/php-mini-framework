<?php

namespace Mini\Framework\Exceptions\Ignition\FlareMiddleware;

use Mini\Framework\Exceptions\Ignition\Recorders\JobRecorder\JobRecorder;
use Spatie\FlareClient\FlareMiddleware\FlareMiddleware;
use Spatie\FlareClient\Report;

class AddJobs implements FlareMiddleware
{
    protected JobRecorder $jobRecorder;

    public function __construct()
    {
        $this->jobRecorder = app(JobRecorder::class);
    }

    public function handle(Report $report, $next)
    {
        if ($job = $this->jobRecorder->getJob()) {
            $report->group('job', $job);
        }

        return $next($report);
    }
}
