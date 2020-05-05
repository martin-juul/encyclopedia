<?php

namespace App\WikiText\Parser;

/**
 * Stores inline elements
 */
class ParserInlineElement
{
    public $startTag;
    public $endTag;
    public $argSep;
    public $argNameSep;
    public $hasArgs;
    public int $argLimit;

    public function __construct(
        string $startTag,
        string $endTag,
        string $argSep = '',
        string $argNameSep = '',
        int $argLimit = 0
    )
    {
        $this->startTag = str_split($startTag);
        $this->endTag = str_split($endTag);
        $this->argSep = str_split($argSep);
        $this->argNameSep = str_split($argNameSep);
        $this->argLimit = $argLimit;
        $this->hasArgs = count($this->argSep) > 0;
    }
}

