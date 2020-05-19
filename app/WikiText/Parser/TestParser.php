<?php
declare(strict_types=1);

namespace App\WikiText\Parser;

use App\Utilities\Extensions\StrExt;
use Illuminate\Support\Str;

class TestParser
{
    private string $input;
    private $res;

    private static $specialTokens = [
        '{',
        '=',
        '<',
        '|',
    ];

    private static $blocks = [
        // name => start tag, end tag
        'infobox' => ['{{Infobox', '}}'],
    ];

    private static $nodes = [
        // name => start tag, end tag, arg limit, arg separator, arg name separator
        'small'            => ['{{small', '}}', 1, null, null],
        'shortDescription' => ['{{short description', '}}', 1, null, null],
    ];

    public function __construct(string $input)
    {
        $this->input = $input;
    }

    public function parse()
    {
        if (isset($this->res)) {
            return $this->res;
        }

        $sections = explode("\n", $this->input);

        foreach ($sections as $i => $section) {
            $buffer = '';
            dump($section);

            if (Str::startsWith($section, '{{')) {
                if (array_key_exists(str_replace('{{', '', $section)))
            }
        }

        return $this->res;
    }

    private function isBlock(string $name): bool
    {
        return array_key_exists($name, static::$blocks);
    }

    private function isNode(string $name): bool
    {
        return array_key_exists($name, static::$nodes);
    }

    private function splitString(string $str)
    {
        return preg_split('//u', $str, -1, PREG_SPLIT_NO_EMPTY);
    }
}
