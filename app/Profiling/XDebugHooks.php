<?php
declare(strict_types=1);

namespace App\Profiling;

trait XDebugHooks
{
    private $xdebugHooksEnabled;

    private function enable()
    {
        if ($this->xdebugHooksEnabled) {
            return;
        }

        if (extension_loaded('Xdebug')) {
            $this->xdebugHooksEnabled = true;
        }
    }

    private function disable()
    {
        $this->xdebugHooksEnabled = false;
    }

    private function hook()
    {
        xdebug_memory_usage();
    }

    public function __call($name, $arguments)
    {
        if ($this->xdebugHooksEnabled === true) {
            $this->hook();
        }

        if (is_callable(['parent', '__call'])) {
            return parent::__call($name, $arguments);
        }

        return call_user_func([$this, $name], ...$arguments);
    }
}
