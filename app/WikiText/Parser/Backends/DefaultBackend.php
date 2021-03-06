<?php
declare(strict_types=1);

namespace App\WikiText\Parser\Backends;

use App\View\Components\InfoBox;
use Log;

class DefaultBackend implements WikiTextBackend
{
    use EncapsulatesInline;

    private $interwiki;

    /**
     * Process an element which has arguments.
     * Links, lists and templates fall under this category
     *
     * @param string $elementName
     * @param array|string $arg
     *
     * @return string
     */
    public function renderWithArgs(string $elementName, $arg): string
    {
        if ($elementName === 'small') {
            dd([$elementName, $arg]);
        }
        $fn = [$this, 'render' . ucfirst($elementName)];

        if (is_callable($fn)) {
            /* If a function is defined to handle this, use it */
            return $fn($arg);
        }

        return $arg[0];
    }

    public function renderLineBlock(string $elementName, array $list): string
    {
        if ($elementName === 'small') {
            dd([$elementName, $arg]);
        }
        $fn = [$this, 'render' . ucfirst($elementName)];

        if (is_callable($fn)) {
            /* If a function is defined to encapsulate this, use it */
            return $fn($elementName, $list);
        }

        return $elementName;
    }

    public function renderOl($token, $list): string
    {
        return $this->renderList($token, $list);
    }

    public function renderUl($token, $list): string
    {
        return $this->renderList($token, $list);
    }

    public function renderDl($token, $list): string
    {
        return $this->renderList($token, $list);
    }

    public function renderH($token, $headings): string
    {
        $outp = '';

        foreach ($headings as $heading) {
            $tag = 'h' . $heading['depth'];
            $outp .= "<$tag>" . $heading['item'] . "</$tag>\n";
        }

        return $outp;
    }

    public function renderSmall($input)
    {
        dd($input);
    }

    public function renderPre($token, $lines): string
    {
        $outpline = [];

        foreach ($lines as $line) {
            $outpline[] = $line['item'];
        }

        return '<pre>' . implode("\n", $outpline) . '</pre>';
    }

    /**
     * Render list and any sub-lists recursively
     *
     * @param string $token The type of list (expect ul, ol, dl)
     * @param array $list   The hierachy representing this list
     *
     * @param int $expectedDepth
     *
     * @return string HTML markup for the list
     */
    public function renderList($token, array $list, $expectedDepth = 1): string
    {
        $outp = '';
        $subtoken = 'li';
        $outp .= "<$token>\n";

        foreach ($list as $item) {
            if ($token === 'dl') {
                $subtoken = $item['char'] === ';' ? 'dt' : 'dd';
            }

            $outp .= "<$subtoken>";
            $diff = $item['depth'] - $expectedDepth;
            /* Some items are undented unusually far ..  */
            if ($diff > 0) {
                $outp .= str_repeat("<$token><$subtoken>", $diff);
            }

            /* Caption of this item */
            $outp .= $item['item'];
            if (count($item['child']) > 0) {
                /* Add children if applicable */
                $outp .= $this->renderList($token, $item['child'], $item['depth'] + 1);
            }

            if ($diff > 0) {
                /* Close above extra encapsulation if applicable */
                $outp .= str_repeat("</$subtoken></$token>", $diff);
            }

            $outp .= "</$subtoken>\n";
        }
        $outp .= "</$token>\n";

        return $outp;
    }

    /**
     * Default rendering of [[link]] or [[link|foo]]
     *
     * @param $arg
     *
     * @return string HTML markup for the link
     */
    public function renderLinkInternal($arg): string
    {
        /* Figure out properties based on arguments */
        if (isset($arg[0])) {
            $destination = $arg[0];
        }
        if (isset($arg[1])) {
            $caption = $arg[1];
        }

        /* Compensate for missing values */
        if (isset($destination) && !isset($caption)) {
            $caption = $destination; // Fill in caption = destination as default
        }

        if (!isset($destination)) {
            if (isset($caption)) {
                $destination = ''; // Empty link
            } else {
                return ''; // Empty link to nowhere (so skip it)
            }
        }

        $info = [
            'url'             => $destination,
            /* You should override getInternalLinkInfo() to set this better according to your application. */
            'title'           => $destination,
            /* Eg [[foo:bar]] links to "foo:bar". */
            'namespace'       => '',
            /* Eg [[foo:bar]] is in namespace 'foo' */
            'target'          => $destination,
            /* Eg [[foo:bar]] has the target "bar" within the namespace. */
            'namespaceignore' => false,
            /* eg [[:File:foo.png]], link to the image don't include it */
            'caption'         => $caption,
            /* The link caption eg [[foo:bar|baz]] has the caption 'baz' */
            'exists'          => true,
            /* Causes class="new" for making red-links */
            'external'        => false,
        ];

        /* Attempt to deduce namespaces */
        if ($destination === '') {
            $split = false;
        } else {
            $split = strpos($destination, ':', 1);
        }

        if (!$split === false) {
            /* We have namespace */
            if (strpos($destination, ':') === 0) { /* Eg [[:category:foo]] */
                $info['namespaceignore'] = true;
                $info['namespace'] = strtolower(substr($destination, 1, $split - 1));
            } else {
                $info['namespace'] = strtolower(substr($destination, 0, $split));
            }

            $split++;
            $info['target'] = substr($destination, $split);

            /* Look up in default interwiki table */
            if ($this->interwiki === false) {
                /* Load as needed */
                $this->loadInterwikiLinks();
            }

            if ($info['namespace'] === 'file') {
                /* Render an image instead of a link if requested */
                $info['url'] = $info['target'];
                $info['caption'] = '';
                return $this->renderFile($info, $arg);
            }

            if (isset($this->interwiki[$info['namespace']])) {
                /* We have a known namespace */
                $site = $this->interwiki[$info['namespace']];
                $info['url'] = str_replace('$1', $info['target'], $site);
            }
        }

        /* Allow the local app to contribute to link properties */
        $info = $this->getInternalLinkInfo($info);
        return '<a href="' . htmlspecialchars($info['url']) . '" title="' . htmlspecialchars($info['title']) . '"' . (!$info['exists'] ? ' class="new"' : '') . '>' . $info['caption'] . '</a>';
    }

    public function renderFile($info, $arg): ?string
    {
        $info['thumb'] = $info['url']; /* Default no no server-side thumbs */
        $info['class'] = '';
        $info['page'] = '';
        $info['caption'] = '';

        $target = $info['target'];
        $pos = strrpos($target, '.');
        if ($pos === false) {
            $ext = '';
        } else {
            $pos++;
            $ext = substr($target, $pos);
        }

        if (\in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'svg'])) {
            /* Image flags parsed. From: http://www.mediawiki.org/wiki/Help:Images */

            /* Named arguments */
            if (isset($arg['link'])) { // |link=
                $info['url'] = $arg['link'];
                $info['link'] = $arg['link'];
                unset($arg['link']);
            }
            if (isset($arg['class'])) { // |class=
                $info['class'] = $arg['class'];
                unset($arg['class']);
            }
            if (isset($arg['alt'])) { // |alt=
                $info['title'] = $arg['alt'];
                unset($arg['alt']);
            }
            if (isset($arg['page'])) { // |alt=
                $info['page'] = $arg['page'];
                unset($arg['page']);
            }

            foreach ($arg as $key => $item) {
                /* Figure out unnamed arguments */
                if (is_numeric($key)) { /* Any unsupported named arguments will be ignored */
                    if (strpos($item, 'px') === 0) {
                        /* Size */
                        // TODO
                    } else {
                        /* Load recognised switches */
                        switch ($item) {
                            case 'frameless':
                                $info['frameless'] = true;
                                break;
                            case 'border':
                                $info['border'] = true;
                                break;
                            case 'frame':
                                $info['frame'] = true;
                                break;
                            case 'thumbnail':
                            case 'thumb':
                                $info['thumbnail'] = true;
                                break;
                            case 'left':
                                $info['left'] = true;
                                break;
                            case 'right':
                                $info['right'] = true;
                                break;
                            case 'center':
                                $info['center'] = true;
                                break;
                            case 'none':
                                $info['none'] = true;
                                break;
                            default:
                                $info['caption'] = $item;
                        }
                    }
                }
            }

            $info = $this->getImageInfo($info);

            if ($info['namespaceignore'] || !$info['exists']) {
                /* Only link to the image, do not display it */
                if ($info['caption'] === '') {
                    $info['caption'] = $info['target'];
                }
                /* Construct link */
                return '<a href="' . htmlspecialchars($info['url']) . '" title="' . htmlspecialchars($info['title']) . '"' . (!$info['exists'] ? ' class="new"' : '') . '>' . $info['caption'] . "</a>";
            }

            $dend = $dstart = '';
            if (isset($info['thumbnail']) || isset($info['frame'])) {
                if (isset($info['right'])) {
                    $align = ' tright';
                } else if (isset($info['left'])) {
                    $align = ' tleft';
                } else {
                    $align = '';
                }
                $dstart = "<div class=\"thumb$align\">";
                if ($info['caption'] !== '') {
                    $dend .= '<div class="thumbcaption">' . htmlspecialchars($info['caption']) . '</div>';
                }
                $dend .= '</div>';
            }

            $classes = null;
            if (isset($info['caption']) && $info['caption'] === 'flagicon') {
                $classes = 'flagicon';
            }

            if ($classes !== null) {
                $classes = 'class="' . $classes . '"';
            }

            /* Construct link */
            return "$dstart<a href=\"" . htmlspecialchars($info['url']) . '"><img ' . $classes . ' src="' . htmlspecialchars($info['thumb']) . '" alt="' . htmlspecialchars($info['title']) . "\" /></a>$dend";
        }

        /* Something unsupported */
        return '<b>(unsupported media file)</b>';
    }

    public function renderFlagicon($arg)
    {
        return $arg[0];
    }

    /**
     * Method to override when providing extra info about an image (basically external URL and thumbnail path)
     *
     * @param $info
     *
     * @return mixed
     */
    public function getImageInfo($info)
    {
        return $info;
    }

    /**
     * Method to override when providing extra info about a link
     *
     * @param $info
     *
     * @return mixed
     */
    public function getInternalLinkInfo($info)
    {
        return $info;
    }

    public function loadInterwikiLinks(): void
    {
        if ($this->interwiki !== false) {
            /* Use loaded interwiki links if they exist */
            return;
        }

        $this->interwiki = [];
        $json = file_get_contents(__DIR__ . '/interwiki.json');

        try {
            $arr = json_decode($json, false, 128, JSON_THROW_ON_ERROR);
            foreach ($arr->query->interwikimap as $site) {
                if (isset($site->prefix, $site->url)) {
                    $this->interwiki[$site->prefix] = $site->url;
                }
            }
        } catch (\JsonException $e) {
            Log::error($e->getMessage(), [
                'exception.code' => $e->getCode(),
                'exception.line' => $e->getLine(),
            ]);
        }
    }

    /**
     * Default rendering of [http://... link] or [http://foo]
     *
     * @param $arg
     *
     * @return string HTML markup for the link
     */
    public function renderLinkExternal($arg): string
    {
        $caption = $arg[1] ?? $arg[0];
        $href = $arg[0];

        return "<a rel='nofollow' class='external' target='_blank' href='{$href}'>{$caption}</a>";
    }

    public function renderInfobox($title, $table)
    {
        return (new InfoBox($title, $table))->render();
    }

    /**
     * Generate HTML for a table
     *
     * @param array $table
     *
     * @return string
     */
    public function renderTable(array $table): string
    {
        if ($table['properties'] === '') {
            $outp = "<table class='table'>" . PHP_EOL;
        } else {
            $properties = str_replace('wikitable', 'table', $table['properties']);
            $outp = '<table ' . trim($properties) . '>' . PHP_EOL;
        }

        foreach ($table['row'] as $row) {
            $outp .= $this->renderRow($row);
        }

        return $outp . "</table>\n";
    }

    /**
     * Render a single row of a table
     *
     * @param array $row
     *
     * @return string
     */
    public function renderRow(array $row): string
    {
        /* Show row with or without attributes */
        if ($row['properties'] === '') {
            $outp = '<tr>' . PHP_EOL;
        } else {
            $outp = '<tr ' . trim($row['properties']) . '>' . PHP_EOL;
        }

        foreach ($row['col'] as $col) {
            /* Show column with or without attributes */
            if (count($col['arg']) !== 0) {
                $outp .= '<' . $col['token'] . ' ' . trim($col['arg'][0]) . '>';
            } else {
                $outp .= '<' . $col['token'] . '>';
            }
            $outp .= $col['content'] . '</' . $col['token'] . ">\n";
        }

        return $outp . "</tr>\n";
    }

    /**
     * Function to override if you want to provide a mechanism for getting templates
     *
     * @param string $template
     *
     * @return string
     */
    public function getTemplateMarkup(string $template): string
    {
        return "[[$template]]";
    }
}

