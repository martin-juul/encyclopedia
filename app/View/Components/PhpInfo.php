<?php

namespace App\View\Components;

use Illuminate\View\Component;

class PhpInfo extends Component
{
    public $section;

    public function __construct(string $section)
    {
        $this->section = $section;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.php-info', [
            'phpInfo' => $this->getPhpInfo(),
        ]);
    }

    protected function getPhpInfo()
    {
        ob_start();
        phpinfo($this->getSection());

        $phpInfo = ob_get_clean();
        $phpInfo = preg_replace('%^.*<body>(.*)</body>.*$%ms', '$1', $phpInfo);

        return $phpInfo;
    }

    protected function getSection()
    {
        $whitelist = [
            'all',
            'configuration',
            'credits',
            'environment',
            'modules',
            'general',
            'license',
            'variables',
        ];

        if (!\in_array($this->section, $whitelist, true)) {
            $whitelist = implode(', ', $whitelist);
            throw new \InvalidArgumentException("$this->section is not a valid section. Available options are: $whitelist");
        }

        switch ($this->section) {
            case 'modules':
                return INFO_MODULES;
            case 'general':
                return INFO_GENERAL;
            case 'credits':
                return INFO_CREDITS;
            case 'configuration':
                return INFO_CONFIGURATION;
            case 'environment':
                return INFO_ENVIRONMENT;
            case 'variables':
                return INFO_VARIABLES;
            case 'license':
                return INFO_LICENSE;
            case 'all':
            default:
                return INFO_ALL;
        }
    }
}
