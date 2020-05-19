<?php
declare(strict_types=1);

namespace App\View\Components\Dashboard;

use Illuminate\View\Component;

class Card extends Component
{
    /** @var string|null */
    private $header;

    /**
     * Create a new component instance.
     *
     * @param string|null $header
     */
    public function __construct(string $header = null)
    {
        $this->header = $header;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\View\View|string
     */
    public function render()
    {
        return view('components.dashboard.card', [
            'header' => $this->header,
        ]);
    }
}
