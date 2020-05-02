<?php

namespace App\Utilities\Parser\WikiText\HtmlElements;

class Paragraph
{
    public string $text;

    public function __construct(string $text)
    {
        $this->text = $text;
    }

    public function toHtml()
    {
        return "<p>{$this->text}</p>";
    }
}
