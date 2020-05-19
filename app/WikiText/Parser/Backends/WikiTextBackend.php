<?php
declare(strict_types=1);

namespace App\WikiText\Parser\Backends;

interface WikiTextBackend
{
    /**
     * Process an element which has arguments.
     * Links, lists and templates fall under this category
     *
     * @param string $elementName
     * @param array|string $arg
     *
     * @return string
     */
    public function renderWithArgs(string $elementName, $arg): string;

    public function renderLineBlock(string $elementName, array $list): string;

    /**
     * Encapsulate inline elements
     *
     * @param string $text        parsed text contained within this element
     * @param string $elementName the name of the element
     *
     * @return string Correct markup for this element
     */
    public function encapsulateElement(string $elementName, string $text): string;

    public function renderInfobox($title, $table);
}
