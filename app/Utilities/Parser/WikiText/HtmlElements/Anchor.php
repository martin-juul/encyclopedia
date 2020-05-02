<?php

namespace App\Utilities\Parser\WikiText\HtmlElements;

use Illuminate\Contracts\Support\Htmlable;

class Anchor implements Htmlable
{
    public ?string $id;
    public string $href;
    public string $destination;
    public string $target;
    public array $classes;

    public function __construct(
        string $href,
        string $destination,
        array $classes = [],
        string $target = 'self',
        ?string $id = null)
    {
        $this->href = htmlspecialchars($href);
        $this->destination = $destination;
        $this->classes = $classes;
        $this->target = $target;
        $this->id = $id;
    }

    public function toHtml()
    {
        $classes = '';
        if (count($this->classes) > 0) {
            $classes = "class='" . implode(' ', $this->classes) . '"';
        }

        $id = '';
        if ($this->id !== null) {
            $id = 'id="' . $id . '"';
        }

        return <<<ANCHOR
<a $id $classes target="{$this->target}" href="{$this->href}">{$this->destination}</a>
ANCHOR;
    }
}
