<?php

namespace Mini\Framework\Exceptions\Ignition\Recorders\DumpRecorder;

use Symfony\Component\VarDumper\Cloner\VarCloner;

class DumpHandler
{
    protected DumpRecorder $dumpRecorder;

    public function __construct(DumpRecorder $dumpRecorder)
    {
        $this->dumpRecorder = $dumpRecorder;
    }

    public function dump(mixed $value): void
    {
        $data = (new VarCloner)->cloneVar($value);

        $this->dumpRecorder->record($data);
    }
}
