<?php
declare(strict_types=1);

namespace App\Profiling;

use App\Exceptions\Profiling\XHProfExtensionMissing;
use App\Models\Sys\PostgresDatabase;
use App\Utilities\PhpConfig;

class XHProf
{
    protected bool $sampleOnly = false;

    public function __construct(PhpConfig $phpConfig)
    {
        if (!config('profiling.xhprof.enabled')) {
            throw new XHProfExtensionMissing;
        }

        if (config('profiling.xhprof.collect_additional_info')) {
            $phpConfig->set('xhprof.collect_additional_info', '1');
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
        // safeguard against non-migrated database
        if (!PostgresDatabase::isMigrated() || !config('profiling.enabled')) {
            return;
        }

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
        if (!config('profiling.enabled')) {
            return null;
        }

        if (!$this->sampleOnly) {
            return xhprof_disable();
        }

        return xhprof_sample_disable();
    }
}
