<?php

namespace App\View\Components\Dashboard\System;

use Illuminate\View\Component;

class Filesystem extends Component
{
    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        [$root, $free, $total] = $this->getFsUsage();

        return view('components.dashboard.system.filesystem', [
            'root'  => $root,
            'free'  => $free,
            'total' => $total,
        ]);
    }

    protected function getFsUsage(): array
    {
        $root = config('filesystems.disks.local.root');

        $free = $this->bytesToSi(disk_free_space($root));
        $total = $this->bytesToSi(disk_total_space($root));

        return [$root, $free, $total];
    }

    private function bytesToSi($bytes): string
    {
        $symbols = ['B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB', 'EiB', 'ZiB', 'Yib'];
        $exp = $bytes ? (floor(log($bytes) / log(1024))) : 0;

        return sprintf('%.2f ' . $symbols[$exp], ($bytes / (1024 ** floor($exp))));
    }
}
