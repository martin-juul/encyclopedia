<?php

namespace App\Utilities\Parser\Wikipedia;

use App\Utilities\Parser\Wikipedia\Models\WikiPage;
use App\Utilities\Parser\XMLParser;

class ArticleMultistreamParser
{
    protected string $path;

    /**
     * FileParser constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function read(string $XPath)
    {
        $parser = new XMLParser($this->path);

        return $parser->read($XPath);
    }

    public function parseNode(\SimpleXMLElement $element): WikiPage
    {
        return new WikiPage($element);
    }
}
