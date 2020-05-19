<?php
declare(strict_types=1);

namespace App\Profiling;

use App\Models\Sys\ProfileReport;

class XHProfReport
{
    public const NO_PARENT = '__xhgui_top__';

    public array $original;

    private array $data;

    private array $indexed = [];

    private array $keys = ['ct', 'mu', 'wt', 'cpu', 'pmu'];
    private array $exclusiveKeys = ['ct', 'mu', 'wt', 'cpu', 'pmu'];
    private int $functionCount = 0;

    /**
     * XHProfReport constructor.
     *
     * @param \App\Models\Sys\ProfileReport|array $profile
     */
    public function __construct($profile)
    {
        if ($profile instanceof ProfileReport) {
            $this->data = $profile->xhprof;
        } else if (array_key_exists('xhprof', $profile)) {
            $this->data = $profile['xhprof'];
        } else if (is_array($profile)) {
            $this->data = $profile;
        } else {
            throw new \InvalidArgumentException('$profile must be an array');
        }

        $this->original = $this->process();
    }

    public function getMainRuntime(): array
    {
        return [
            'cpu'               => $this->original['main()']['cpu'] ?? null,
            'memory_usage'      => $this->original['main()']['mu'] ?? null,
            'peak_memory_usage' => $this->original['main()']['pmu'] ?? null,
            'wall_time'         => $this->original['main()']['wt'] ?? null,
        ];
    }

    /**
     * Convert the raw data into a flatter list that is easier to use.
     *
     * This removes some of the parentage detail as all calls of a given
     * method are aggregated. We are not able to maintain a full tree structure
     * in any case, as xhprof only keeps one level of detail.
     *
     * @return array
     */
    private function process(): array
    {
        $result = [];

        foreach ($this->data as $name => $values) {
            [$parent, $func] = $this->splitName($name);

            // Generate collapsed data.
            if (isset($result[$func])) {
                $result[$func] = $this->sumKeys($result[$func], $values);
                $result[$func]['parents'][] = $parent;
            } else {
                $result[$func] = $values;
                $result[$func]['parents'] = [$parent];
            }

            // Build the indexed data.
            if ($parent === null) {
                $parent = self::NO_PARENT;
            }
            if (!isset($this->_indexed[$parent])) {
                $this->indexed[$parent] = [];
            }
            $this->indexed[$parent][$func] = $values;
        }

        return $result;
    }

    /**
     * Sum up the values in $this->_keys;
     *
     * @param array $a The first set of profile data
     * @param array $b The second set of profile data.
     *
     * @return array Merged profile data.
     */
    protected function sumKeys($a, $b)
    {
        foreach ($this->keys as $key) {
            if (!isset($a[$key])) {
                $a[$key] = 0;
            }
            $a[$key] += $b[$key] ?? 0;
        }
        return $a;
    }

    /**
     * Split a key name into the parent==>child format.
     *
     * @param string $name The name to split.
     *
     * @return array An array of parent, child. parent will be null if there
     *    is no parent.
     */
    private function splitName($name): array
    {
        $a = explode("==>", $name);
        if (isset($a[1])) {
            return $a;
        }
        return [null, $a[0]];
    }

}
