<?php

namespace App\Utilities\Parser\WikiText\HtmlElements;

use Illuminate\Contracts\Support\Htmlable;

class Italic implements Htmlable
{
    public string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function toHtml()
    {
        return "<i>{$this->text}</i>";
    }
}
