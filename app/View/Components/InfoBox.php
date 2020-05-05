<?php

namespace App\View\Components;

use Illuminate\View\Component;

class InfoBox extends Component
{
    private $title;
    private array $rows;

    /**
     * Create a new component instance.
     *
     * @param $title
     * @param $rows
     */
    public function __construct($title, $rows)
    {
        $this->title = $title;
        $this->rows = $rows;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.info-box', [
            'title' => $this->title,
            'rows'  => $this->rows,
        ]);
    }
}
