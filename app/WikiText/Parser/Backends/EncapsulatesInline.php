<?php

namespace App\WikiText\Parser\Backends;

trait EncapsulatesInline
{
    /**
     * Encapsulate inline elements
     *
     * @param string $text        parsed text contained within this element
     * @param string $elementName the name of the element
     *
     * @return string Correct markup for this element
     */
    public function encapsulateElement(string $elementName, string $text): string
    {
        $fn = [$this, 'encapsulate' . ucfirst($elementName)];

        if (is_callable($fn)) {
            /* If a function is defined to encapsulate this, use it */
            return $fn($text);
        }

        return $text;
    }

    /**
     * Default encapsulation for '''bold'''
     *
     * @param string $text Text to make bold
     *
     * @return string
     */
    public function encapsulateBold($text): string
    {
        return "<b>{$text}</b>";
    }

    /**
     * Default encapsulation for ''italic''
     *
     * @param string $text Text to make bold
     *
     * @return string
     */
    public function encapsulateItalic($text): string
    {
        return "<i>{$text}</i>";
    }

    public function encapsulateParagraph($text): string
    {
        return "<p>{$text}</p>" . PHP_EOL;
    }
}
