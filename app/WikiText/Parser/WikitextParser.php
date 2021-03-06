<?php
declare(strict_types=1);

namespace App\WikiText\Parser;

use App\WikiText\Parser\Backends\DefaultBackend;
use League\ISO3166\ISO3166;

class WikitextParser
{
    /**
     * Depth of template includes to put up with.
     * Set to 0 to disallow inclusion, negative to remove the limit.
     */
    public const MAX_INCLUDE_DEPTH = 32;

    /** @var \App\WikiText\Parser\Backends\WikiTextBackend */
    public static $backend;

    private static $inline;
    private static $lineBlock;
    private static $tableBlock;
    private static $tableStart;

    private static $inlineLookup;
    private static $inlineChars = [
        '['  => true,
        '\'' => true,
        ']'  => true,
        "\n" => true,
        '='  => true,
        '*'  => true,
        '#'  => true,
        ':'  => true,
        '|'  => true,
        '~'  => true,
        ' '  => true,
        '_'  => true,
    ];

    private static $preprocessor;
    private static $preprocessorChars = [
        '<' => true,
        '=' => true,
        '|' => true,
        '{' => true,
    ];

    private static $initialised = false;

    /* These are set as a result of parsing */

    /**
     * Wikitext after preprocessor had a go at it.
     * @var string
     */
    public $preprocessed;

    /**
     * Wikitext of result
     *
     * @var string
     */
    public $result;

    private array $params;

    /**
     * Initialise a new parser object and parse a standalone document.
     * If templates are included, each will processed by a different instance of this object
     *
     * @param string $text The text to parse
     * @param array $params
     */
    public function __construct(string $text, array $params = [])
    {
        if (self::$initialised === false) {
            isset($params['sharedVars']) ? self::init($params['sharedVars']) : self::init();
        }

        $this->params = $params;
        $this->preprocessed = $this->preprocessText(self::explodeString($text));

        /* Now divide into paragraphs */
        // TODO operate on arrays instead of strings here
        $sections = explode("\n\n", str_replace("\r\n", "\n", $this->preprocessed));

        $newtext = [];
        foreach ($sections as $section) {
            /* Newlines at the start/end have special meaning (compare to how this is called from parseLineBlock) */
            $sectionChars = self::explodeString("\n" . $section);
            $result = $this->parseInline($sectionChars, 'p');
            $newtext[] = $result['parsed'];
        }

        $this->result = implode($newtext);
    }

    /**
     * Definitions for tokens with special meaning to the parser
     *
     * @param array $sharedVars
     */
    private static function init(array $sharedVars = []): void
    {
        // Table elements. These are parsed separately to the other elements
        self::$tableStart = new ParserInlineElement('{|', '|}');

        self::$tableBlock = [
            'tr'      => new ParserTableElement('|-', '', '', ''),
            'th'      => new ParserTableElement('!', '|', '!!', 1),
            'td'      => new ParserTableElement('|', '|', '||', 1),
            'caption' => new ParserTableElement('|+', '', '', 0),
        ];

        /* Inline elements. These are parsed recursively and can be nested as deeply as the system will allow. */
        self::$inline = [
            'small'        => new ParserInlineElement('[[small', ']]', '', ''),
            'nothing'      => new ParserInlineElement('', ''),
            'td'           => new ParserInlineElement('', ''), // Just used as a marker
            'flagicon'     => new ParserInlineElement('[[flagicon', ']]', '|', '=', 1),
            'linkInternal' => new ParserInlineElement('[[', ']]', '|', '='),
            'linkExternal' => new ParserInlineElement('[', ']', ' ', '', 1),
            'bold'         => new ParserInlineElement("'''", "'''"),
            'italic'       => new ParserInlineElement("''", "''"),
            'switch'       => new ParserInlineElement('__', '__'),
        ];

        // Create lookup table for efficiency
        self::$inlineLookup = self::elementLookupTable(self::$inline);
        self::$backend = new DefaultBackend();

        // Line-block elements.
        // These are characters which have a special meaning at the start of lines,
        // and use the next end-line as a close tag.
        self::$lineBlock = [
            'pre' => new ParserLineBlockElement([' '], [], 1, false),
            'ul'  => new ParserLineBlockElement(['*'], [], 32, true),
            'ol'  => new ParserLineBlockElement(['#'], [], 32, true),
            'dl'  => new ParserLineBlockElement([':', ';'], [], 32, true),
            'h'   => new ParserLineBlockElement(['='], ['='], 6, false),
        ];

        self::$preprocessor = [
            'small'       => new ParserInlineElement('{{small|', '}}', '', ''),
            'infobox'     => new ParserInlineElement('{{infobox', "}}", '|', '='),
            'noinclude'   => new ParserInlineElement('<noinclude>', '</noinclude>'),
            'includeonly' => new ParserInlineElement('<includeonly>', '</includeonly>'),
            'arg'         => new ParserInlineElement('{{{', '}}}', '|', '', 1),
            'flagicon'    => new ParserInlineElement('{{flagicon', '}}', '|', ''),
            'template'    => new ParserInlineElement('{{', '}}', '|', '='),
            'comment'     => new ParserInlineElement('<!--', '-->'),
        ];

        self::$initialised = true;
    }

    /**
     * Parse a given document/page of text (main entry point)
     *
     * @param string $text
     *
     * @return string
     */
    public static function parse(string $text): string
    {
        return (new static($text))->result;
    }

    private static function elementLookupTable(array $elements): array
    {
        $lookup = [];
        foreach ($elements as $key => $token) {
            if (count($token->startTag) !== 0) {
                $c = $token->startTag[0];
                if (!isset($lookup[$c])) {
                    $lookup[$c] = [];
                }
                $lookup[$c][$key] = $elements[$key];
            }
        }
        return $lookup;
    }

    /**
     * Handle template arguments and other oddities.
     * This section of the parser is single-pass and linear,
     * with the exception of the part which substitutes templates
     *
     * @param array $textChars
     * @param mixed $arg        Arguments (applies only to templates)
     * @param boolean $included true if the text is included, false otherwise
     *
     * @param int $depth
     *
     * @return string
     */
    private function preprocessText(array $textChars, array $arg = [], bool $included = false, int $depth = 0)
    {
        $parsed = '';
        $len = count($textChars);

        for ($i = 0; $i < $len; $i++) {
            $hit = false;
            $c = $textChars[$i];

            if (!isset(self::$preprocessorChars[$c])) {
                /* Fast exit for characters that do not start a tag. */
                // TODO Could work faster if we didn't concatenate each character
                $parsed .= $c;
                continue;
            }

            foreach (self::$preprocessor as $key => $child) {
                if (self::tagIsAt($child->endTag, $textChars, $i)) {
                    if (($key === 'includeonly' && $included) || ($key === 'noinclude' && !$included)) {
                        $hit = true;
                        $i += count($child->endTag);

                        /* Ignore expected end-tags */
                        break;
                    }
                }

                if (self::tagIsAt($child->startTag, $textChars, $i)) {
                    /* Hit a symbol. Parse it and keep going after the result */
                    $hit = true;
                    $i += count($child->startTag);

                    if (($key === 'includeonly' && $included) || ($key === 'noinclude' && !$included)) {
                        /* If this is a good tag, ignore it! */
                        break;
                    }

                    /* Seek until end tag, looking for splitters */
                    $innerArg = [];
                    $innerBuffer = '';
                    $innerCurKey = '';

                    /** @noinspection SuspiciousLoopInspection */
                    for ($i = $i; $i < $len; $i++) {
                        $innerHit = false;

                        if (self::tagIsAt($child->endTag, $textChars, $i)) {
                            $i += count($child->endTag);
                            /* Clear buffers now */
                            if ($innerCurKey === '') {
                                $innerArg[] = $innerBuffer;
                            } else {
                                $innerArg[$innerCurKey] = $innerBuffer;
                            }

                            /* Figure out what to do with data */
                            $innerCurKey = array_shift($innerArg);
                            if ($key === 'arg') {
                                if (is_numeric($innerCurKey)) {
                                    --$innerCurKey; /* Because the associative array will be starting at 0 */
                                }
                                if (isset($arg[$innerCurKey])) {
                                    $parsed .= $arg[$innerCurKey];      // Use arg value if set
                                } else if (count($innerArg) > 0) {
                                    $parsed .= array_shift($innerArg);  // Otherwise use embedded default if set
                                }
                            } else if (($key === 'template' && $isInfobox = false) || $isInfobox = $key === 'infobox') {
                                /* Load wikitext of template, and preprocess it */
                                if (self::MAX_INCLUDE_DEPTH < 0 || $depth < self::MAX_INCLUDE_DEPTH) {
                                    if ($isInfobox) {
                                        $innerCurKey = ltrim($innerCurKey);
                                    }

                                    $markup = trim(self::$backend->getTemplateMarkup($innerCurKey));
                                    $parsed .= $this->preprocessText(self::explodeString($markup), $innerArg, true, $depth + 1);
                                }
                            } else if ($key === 'flagicon') {
                                /* Load arg of flagicon, and preprocess it */
                                $this->processFlagIcon($innerArg[0], $parsed);
                            }

                            $innerCurKey = ''; // Reset key
                            $innerBuffer = ''; // Reset parsed values
                            break; /* Stop inner loop(hit) */
                        }

                        /* Argument splitting -- A dumber, non-recursive version of what is used in ParseInline() */
                        if ($child->hasArgs && ($child->argLimit === 0 || $child->argLimit > count($innerArg))) {
                            if (self::tagIsAt($child->argSep, $textChars, $i)) {
                                /* Hit argument separator */
                                if ($innerCurKey === '') {
                                    $innerArg[] = $innerBuffer;
                                } else {
                                    $innerArg[$innerCurKey] = $innerBuffer;
                                }
                                $innerCurKey = ''; // Reset key
                                $innerBuffer = ''; // Reset parsed values
                                $i += count($child->argSep) - 1;
                                $innerHit = true;
                            } else if ($innerCurKey === '' && self::tagIsAt($child->argNameSep, $textChars, $i)) {
                                /* Hit name/argument splitter */
                                $innerCurKey = $innerBuffer; // Set key
                                $innerBuffer = '';  // Reset parsed values
                                $i += count($child->argNameSep) - 1;
                                $innerHit = true;
                            }
                        }

                        if (!$innerHit) {
                            /* Append non-matching characters to buffer as we go */
                            $innerBuffer .= $textChars[$i];
                        }
                    }
                }
            }

            /* Add non-affected characters as we go */
            if (!$hit) {
                $parsed .= $c;
            } else {
                --$i;
            }
        }

        return $parsed;
    }

    /**
     * @param array $tag
     * @param array $textChars
     * @param int $position
     *
     * @return bool
     */
    private static function tagIsAt(array $tag, array $textChars, int $position): bool
    {
        if (!isset($textChars[$position], $tag[0])) {
            return false;
        }

        if ($textChars[$position] !== $tag[0]) {
            // Fast exit for common case
            return false;
        }
        // More detailed checks for other cases
        $tagLen = count($tag);
        $strLen = count($textChars);
        $match = $position + $tagLen <= $strLen && $tagLen > 0;

        for ($i = 1; $i < $tagLen && $match; $i++) {
            if ($textChars[$position + $i] !== $tag[$i]) {
                $match = false;
                break;
            }
        }

        return $match;
    }

    /**
     * Parse a block of wikitext looking for inline tokens, indicating the start of an element.
     * Calls itself recursively to search inside those elements when it finds them
     *
     * @param array $textChars
     * @param string $token The name of the current inline element, if inside one.
     * @param int $idxFrom
     *
     * @return array
     */
    private function parseInline(array $textChars, string $token = '', $idxFrom = 0)
    {
        /* Quick escape if we've run into a table */
        $inParagraph = false;

        if ($token === '' || !isset(self::$inline[$token])) {
            /* Default to empty token if none is set (these have no end token, ensuring there will be no remainder after this runs) */
            if ($token === 'p') {
                /* Blocks of text here need to be encapsulated in paragraph tags */
                $inParagraph = true;
            }

            $inlineElement = self::$inline['nothing'];
        } else {
            $inlineElement = self::$inline[$token];
        }

        $parsed = ''; // For completely parsed text
        $buffer = ''; // For text which may still be encapsulated or chopped up
        $remainder = '';

        $arg = [];
        $curKey = '';

        $len = count($textChars);
        for ($i = $idxFrom; $i < $len; $i++) {
            /* Looping through each character */
            $hit = false; // State so that the last part knows whether to simply append this as an unmatched character
            $c = $textChars[$i];
            if (!isset(self::$inlineChars[$c])) {
                // Fast exit for characters that do not start a tag.
                // TODO Could work faster if we didn't concatenate each character
                $buffer .= $c;
                continue;
            }

            /* Looking for this element's close-token */
            if (self::tagIsAt($inlineElement->endTag, $textChars, $i)) {
                /* Hit a close tag: Stop parsing here, return the remainder, and let the parent continue */
                $start = $i + count($inlineElement->endTag);

                if ($inlineElement->hasArgs) {
                    /* Handle arguments if needed */
                    if ($curKey === '') {
                        $arg[] = $buffer;
                    } else {
                        $arg[$curKey] = $buffer;
                    }
                    $buffer = self::$backend->renderWithArgs($token, $arg);
                }

                /* Clean up and quit */
                $parsed .= $buffer; /* As far as I can tell $inPargraph should always be false here? */

                return ['parsed' => $parsed, 'remainderIdx' => $start];
            }

            /* Next priority is looking for this element's argument tokens if applicable */
            if (($inlineElement->argLimit === 0) || ($inlineElement->argLimit > count($arg))) {
                if ($inlineElement->hasArgs && self::tagIsAt($inlineElement->argSep, $textChars, $i)) {
                    /* Hit argument separator */
                    if ($curKey === '') {
                        $arg[] = $buffer;
                    } else {
                        $arg[$curKey] = $buffer;
                    }

                    $curKey = ''; // Reset key
                    $buffer = ''; // Reset parsed values
                    /* Handle position properly */
                    $i += count($inlineElement->argSep) - 1;
                    $hit = true;
                } else if ($curKey === '' && self::tagIsAt($inlineElement->argNameSep, $textChars, $i)) {
                    /* Hit name/argument splitter */
                    $curKey = $buffer; // Set key
                    $buffer = '';  // Reset parsed values
                    /* Handle position properly */
                    $i += count($inlineElement->argNameSep) - 1;
                    $hit = true;
                }
            }

            /* Looking for new open-tokens */
            if (self::inlineLookupHas($c)) {
                /* There are inline elements which start with this character. Check each one,.. */
                foreach (self::$inlineLookup[$c] as $key => $child) {
                    if (!$hit && self::tagIsAt($child->startTag, $textChars, $i)) {
                        /* Hit a symbol. Parse it and keep going after the result */
                        $start = $i + count($child->startTag);

                        /* Regular, recursively-parsed element */
                        $result = $this->parseInline($textChars, $key, $start);
                        $buffer .= self::$backend->encapsulateElement($key, $result['parsed']);
                        $i = $result['remainderIdx'] - 1;
                        $hit = true;
                    }
                }
            }

            if (!$hit) {
                if ($c === "\n" && $i < $len - 1) {
                    if (self::tagIsAt(self::$tableStart->startTag, $textChars, $i + 1)) {
                        $hit = true;
                        $start = $i + 1 + count(self::$tableStart->startTag);
                        $key = 'table';
                    } else {
                        /* Check for non-table line-based stuff coming up next, each time \n is found */
                        $next = $textChars[$i + 1];
                        foreach (self::$lineBlock as $key => $block) {
                            foreach ($block->startChar as $char) {
                                if (!$hit && $next === $char) {
                                    $hit = true;
                                    $start = $i + 1;
                                    break 2;
                                }
                            }
                        }
                    }

                    if ($hit && !empty($start)) {
                        /* Go over what's been found */
                        if (!empty($key) && $key === 'table') {
                            $result = $this->parseTable($textChars, $start);
                        } else {
                            /* Let parseLineBlock take care of this on a per-line basis */
                            $result = $this->parseLineBlock($textChars, $key, $start);
                        }
                        if ($buffer !== '') {
                            /* Something before this was part of a paragraph */
                            $parsed .= self::$backend->encapsulateElement('paragraph', $buffer);
                            $inParagraph = true;
                        }
                        $buffer = '';
                        /* Now append this non-paragraph element */
                        $parsed .= $result['parsed'];
                        $i = $result['remainderIdx'] - 1;
                    }

                    /* Other \n-related things if it wasn't as exciting as above */
                    if ($buffer !== '' && !$hit) {
                        /* Put in a space if it is not going to be the first thing added. */
                        $buffer .= ' ';
                    }
                } else {
                    /* Append character to parsed output if it was not part of some token */
                    $buffer .= $c;
                }
            }

            if ($token === 'td') {
                /* We only get here from table syntax if something else was being parsed, so we can quit here */
                $parsed = $buffer;
                return ['parsed' => $parsed, 'remainderIdx' => $i];
            }
        }

        /* Need to throw argument-driven items at the backend first here */
        if ($inlineElement->hasArgs) {
            if ($curKey === '') {
                $arg[] = $buffer;
            } else {
                $arg[$curKey] = $buffer;
            }
            $buffer = self::$backend->renderWithArgs($token, $arg);
        }

        if ($inParagraph && $buffer !== '') {
            /* Something before this was part of a paragraph */
            $parsed .= self::$backend->encapsulateElement('paragraph', $buffer);
        } else {
            $parsed .= $buffer;
        }

        return ['parsed' => $parsed, 'remainderIdx' => $i];
    }

    /**
     * Parse block of wikitext known to be starting with a line-based token
     *
     * @param array $textChars
     * @param string $token name of the LineBlock token which we suspect
     * @param int $fromIdx
     *
     * @return array
     */
    private function parseLineBlock(array $textChars, string $token, $fromIdx = 0)
    {
        /* Block element we are using */
        $lineBlockElement = self::$lineBlock[$token];

        // Loop through lines
        $lineStart = $fromIdx;
        $list = [];

        while (($lineLen = self::getLineLen($textChars, $lineStart)) !== false) {
            $startTokenLen = self::countChar($lineBlockElement->startChar, $textChars, $lineStart, $lineBlockElement->limit);
            if ($startTokenLen === 0) {
                /* Wind back to include "\n" if the next line is not a list item. This is not expected
                 * to trigger on the first iteration, since line-block tags were found for calling this method.
                 */
                --$lineStart;
                break;
            }

            $char = $textChars[$lineStart + $startTokenLen - 1];
            $endTokenLen = 0;

            if (count($lineBlockElement->endChar) > 0) {
                /* Also need to cut off end letters, such as in == Heading == */
                $endTokenLen = self::countCharReverse($lineBlockElement->endChar, $textChars, $lineStart + $startTokenLen, $lineStart + $lineLen - 1);
            }

            /* Remainder of the line */
            $lineChars = array_slice($textChars, $lineStart + $startTokenLen, $lineLen - $startTokenLen - $endTokenLen);
            $result = $this->parseInline($lineChars);
            $list[] = ['depth' => $startTokenLen, 'item' => $result['parsed'], 'char' => $char];
            /* Move along to start of next line */
            $lineStart += $lineLen + 1;
        }

        if ($lineBlockElement->nestTags) {
            /* Hierarchy-ify nestable lists */
            $list = self::makeList($list);
        }

        $parsed = self::$backend->renderLineBlock($token, $list);

        return ['parsed' => $parsed, 'remainderIdx' => $lineStart];
    }

    /**
     * Special handling for tables, uniquely containing both per-line and recursively parsed elements
     *
     * @param array $textChars
     * @param int $fromIdx
     *
     * @return array :string parsed and remaining text
     */
    private function parseTable(array $textChars, $fromIdx = 0)
    {
        $lineLen = self::getLineLen($textChars, $fromIdx);
        $propertiesChars = array_slice($textChars, $fromIdx, $lineLen);
        $lineStart = $lineLen + 1;

        $table = [];
        $table['properties'] = implode($propertiesChars);
        $table['row'] = [];

        while (($lineLen = self::getLineLen($textChars, $lineStart)) !== false) {
            if (self::tagIsAt(self::$tableStart->endTag, $textChars, $lineStart)) {
                $lineStart += $lineLen + 1;
                break;
            }

            $hit = false;

            foreach (self::$tableBlock as $token => $block) {
                /* Looking for matching per-line elements */
                if (!$hit && self::tagIsAt($block->lineStart, $textChars, $lineStart)) {
                    $hit = true;
                    break;
                }
            }

            if ($hit && !empty($block)) {
                /* Move cursor along to skip the token */
                $tokenLen = count($block->lineStart);
                $contentStart = $lineStart + $tokenLen;
                $contentLen = $lineLen - $tokenLen;

                if ((!empty($token) && $token === 'td') || (!empty($token) && $token === 'th')) {
                    if (!isset($tmpRow)) {
                        /* Been given a cell before a row. Make a row first */
                        $tmpRow = ['properties' => '', 'col' => []];
                    }
                    /* Clobber the remaining text together and throw it to the cell parser */
                    $result = $this->parseTableCells($token, $textChars, $contentStart, $tmpRow['col']);
                    $lineStart = $result['remainderIdx'];
                    $lineLen = -1;
                    $tmpRow['col'] = $result['col'];
                } else if (!empty($token) && $token === 'tr') {
                    $contentChars = array_slice($textChars, $contentStart, $contentLen);

                    if (isset($tmpRow)) {
                        /* Append existing row to table (if one exists) */
                        /** @noinspection UnsupportedStringOffsetOperationsInspection */
                        $table['row'][] = $tmpRow;
                    }

                    /* Clearing current row and set properties */
                    $tmpRow = [
                        'properties' => implode($contentChars),
                        'col'        => [],
                    ];
                }
            }
            /* Move along to start of next line */
            $lineStart += $lineLen + 1;
        }

        if (isset($tmpRow)) {
            /* Tack on the last row */
            /** @noinspection UnsupportedStringOffsetOperationsInspection */
            $table['row'][] = $tmpRow;
        }

        $parsed = self::$backend->renderTable($table);

        return ['parsed' => $parsed, 'remainderIdx' => $lineStart];
    }

    private static function getLineLen(array $textChars, int $position)
    {
        $textCharCount = count($textChars);
        /* Return number of characters in line, or FALSE if the string is depleted */
        for ($i = $position; $i < $textCharCount; $i++) {
            if ($textChars[$i] === "\n") {
                return $i - $position;
            }
        }
        return $position < count($textChars) ? count($textChars) - $position : false;
    }

    /**
     * Retrieve columns started in this line of text
     *
     * @param string $token  Type of cells we are looking at (th or td)
     * @param array $textChars
     * @param int $from
     * @param array $columns Columns which have already been found in this row
     *
     * @return array :string parsed and remaining text
     */
    private function parseTableCells(string $token, array $textChars, int $from, array $columns)
    {
        $tableElement = self::$tableBlock[$token];
        $len = count($textChars);

        $tmpCol = ['arg' => [], 'content' => '', 'token' => $token];
        $argCount = 0;
        $buffer = '';

        /* Loop through each character */
        for ($i = $from; $i < $len; $i++) {
            $hit = false;
            /* We basically detect the start of any inline/lineblock/table elements and,
               knowing that the inline parser knows how to handle them, throw then wayward
            */
            $char = $textChars[$i];
            if (self::inlineLookupHas($char)) {
                /* There are inline elements which start with this character. Check each one,.. */
                foreach (self::$inlineLookup[$char] as $key => $child) {
                    if (!$hit && self::tagIsAt($child->startTag, $textChars, $i)) {
                        $hit = true;
                    }
                }
            }
            if ($char === "\n") {
                if (self::tagIsAt(self::$tableStart->startTag, $textChars, $i + 1)) {
                    /* Table is coming up */
                    $hit = true;
                } else {
                    /* LineBlocks like lists and headings*/
                    $next = $textChars[$i + 1];
                    foreach (self::$lineBlock as $key => $block) {
                        foreach ($block->startChar as $startChar) {
                            if (!$hit && $next === $startChar) {
                                $hit = true;
                                break 2;
                            }
                        }
                    }
                }
            }

            if ($hit) {
                /* Parse whatever it is and return here */
                $start = $i;
                $result = $this->parseInline($textChars, 'td', $start);
                $buffer .= $result['parsed'];
                // TODO was -1 before, seems to work well though
                $i = $result['remainderIdx'];
            }

            if (!$hit && self::tagIsAt($tableElement->inlinesep, $textChars, $i)) {
                /* Got column separator, so this column is now finished */
                $tmpCol['content'] = $buffer;
                $columns[] = $tmpCol;

                /* Reset for the next */
                $tmpCol = ['arg' => [], 'content' => '', 'token' => $token];
                $buffer = '';
                $hit = true;
                $i += count($tableElement->inlinesep) - 1;
                $argCount = 0;
            }

            if (!$hit && $argCount < ($tableElement->limit) && self::tagIsAt($tableElement->argsep, $textChars, $i)) {
                /* Got argument separator. Shift off the last argument */
                $tmpCol['arg'][] = $buffer;
                $buffer = '';
                $hit = true;
                $i += count($tableElement->argsep) - 1;
                $argCount++;
            }

            if (!$hit) {
                $char = $textChars[$i];
                if ($char === "\n") {
                    /* Checking that the next line isn't starting a different element of the table */
                    foreach (self::$tableBlock as $key => $block) {
                        if (self::tagIsAt($block->lineStart, $textChars, $i + 1)) {
                            /* Next line is more table syntax. bail otu and let something else handle it */
                            break 2;
                        }
                    }
                }
                $buffer .= $char;
            }
        }

        /* Put remaining buffers in the right place */
        $tmpCol['content'] = $buffer;
        $columns[] = $tmpCol;
        $start = $i + 1;

        return ['col' => $columns, 'remainderIdx' => $start];
    }

    private static function countChar(array $chars, array $text, int $position, int $max = 0): int
    {
        $i = 0;
        while ($i < $max && isset($text[$position + $i]) && in_array($text[$position + $i], $chars, true)) {
            $i++;
        }
        return $i;
    }

    private static function countCharReverse(array $chars, array $text, int $min, int $position): int
    {
        $i = 0;
        while (($position - $i) > $min && isset($text[$position - $i]) && in_array($text[$position - $i], $chars, true)) {
            $i++;
        }
        return $i;
    }

    /**
     * Create a list from what we found in parseLineBlock(), returning all elements.
     *
     * @param array $lines
     *
     * @return array
     */
    private static function makeList(array $lines)
    {
        $list = self::findChildren($lines, 0, -1);
        return $list['child'];
    }

    /**
     * Recursively nests list elements inside each other, forming a hierarchy to traverse when rendering
     *
     * @param array $lines
     * @param $depth
     * @param $minKey
     *
     * @return array[]
     */
    private static function findChildren(array $lines, $depth, $minKey)
    {
        $children = [];
        $not = [];

        foreach ($lines as $key => $line) {
            /* Loop through for candidates */
            if ($key > $minKey) {
                if ($line['depth'] > $depth) {
                    $children[$key] = $line;
                    unset($lines[$key]);
                } else if ($line['depth'] <= $depth) {
                    break;
                }
            }
        }

        /* For each child, list its children */
        foreach ($children as $key => $child) {
            if (isset($children[$key])) {
                $result = self::findChildren($children, $child['depth'], $key);
                $children[$key]['child'] = $result['child'];

                /* We know that all of this list's children are NOT children of this item (directly), so remove them from our records. */
                foreach ($result['child'] as $notkey => $notchild) {
                    unset($children[$notkey]);
                    $not[$notkey] = true;
                }

                /* And same for non-direct children reported above */
                foreach ($result['not'] as $notkey => $foo) {
                    unset($children[$notkey]);
                    $not[$notkey] = true;
                }
            }
        }

        return ['child' => $children, 'not' => $not];
    }

    /**
     * @param int|float|string|null $char
     *
     * @return bool
     */
    private static function inlineLookupHas($char): bool
    {
        return isset(self::$inlineLookup[$char]);
    }

    /**
     * @param string $string
     *
     * @return array|false|string[]
     */
    private static function explodeString(string $string)
    {
        return preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @param $str
     * @param string $parsed
     */
    private function processFlagIcon($str, string &$parsed): void
    {
        try {
            $isoCountry = (new ISO3166)->alpha3(strtolower($str));

            $link = asset('/assets/icons/country-flag/' . strtolower($isoCountry['alpha2']) . '.svg');

            $parsed .= '[[File:' . $link . '|flagicon|url=' . $link . ']]';
        } catch (\Exception $e) {
            \Log::error($e->getMessage(), [
                'exception.code' => $e->getCode(),
                'exception.line' => $e->getLine(),
            ]);
        }
    }
}
