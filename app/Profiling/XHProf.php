<?php

namespace App\Profiling;

use App\Exceptions\Profiling\XHProfExtensionMissing;

class XHProf
{
    protected bool $sampleOnly = false;

    public function __construct()
    {
        if (!config('profiling.xhprof.enabled')) {
            throw new XHProfExtensionMissing;
        }

        if (config('profiling.xhprof.collect_additional_info')) {
            ini_set('xhprof.collect_additional_info', 1);
        }
    }

    /**
     * @param bool $sampleOnly
     */
    public function setSampleOnly(bool $sampleOnly): void
    {
        $this->sampleOnly = $sampleOnly;
    }

    public function start(?int $flags = null, array $options = []): void
    {
        if (!$this->sampleOnly) {
            if (!$flags) {
                /** @noinspection CallableParameterUseCaseInTypeContextInspection */
                $flags = config('profiling.xhprof.flags');
            }

            xhprof_enable($flags, $options);
        } else {
            xhprof_sample_enable();
        }
    }

    public function stop(): ?array
    {
        if (!$this->sampleOnly) {
            return xhprof_disable();
        }

        return xhprof_sample_disable();
    }
}
