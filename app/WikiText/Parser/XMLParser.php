<?php
declare(strict_types=1);

namespace App\WikiText\Parser;

use App\Utilities\Filesystem\Path;
use Illuminate\Support\Str;

class XMLParser
{
    /** @var \XMLReader */
    private $reader;

    /**
     * Create a new instance of the Reader
     *
     * @param string $filePath
     */
    public function __construct(string $filePath)
    {
        if (!Path::isReadable($filePath)) {
            throw new \InvalidArgumentException("Failed to open file: $filePath - Permission denied.");
        }

        if (Str::endsWith($filePath, ['bz2', 'bzip2'])) {
            $filePath = "compress.bzip2://$filePath";
        }

        $this->reader = new \XMLReader;
        // Open the reader
        $this->reader->open($filePath, null, LIBXML_NOBLANKS | LIBXML_COMPACT);
    }

    /**
     * Get an XML representation from an XML node using XMLReader, Generator and SimpleXMLElement
     *
     * @param string $XPath node XPath
     *
     * @return \Generator
     */
    public function read(string $XPath): \Generator
    {
        if (empty($XPath)) {
            throw new \InvalidArgumentException("Node path can't be empty" .
                __CLASS__ . ' ' .
                __FUNCTION__ . ' method');
        }

        // Set the path traversed by the reader
        $pathNode = '';
        // Start to read from the first node
        while ($this->reader->read()) {
            // Name and type of the current node
            $nodeName = $this->reader->name;
            $nodeType = $this->reader->nodeType;
            /**
             * Checks if the node is a "start element"
             * @see https://secure.php.net/manual/es/class.xmlreader.php
             */
            if (\XMLReader::ELEMENT === $nodeType) {
                if (empty($pathNode)) {
                    $pathNode = $nodeName;
                } else {
                    $newPath = implode('/', [$pathNode, $nodeName]);
                    // Add the name of the node to the traversed path
                    if (false !== strpos($XPath, $newPath)) {
                        $pathNode = $newPath;
                    }
                }
                // Compare traversed path with current node
                if ($pathNode === $XPath) {
                    // Delete the node name from the traversed path
                    $pathNode = preg_replace("/\/?{$nodeName}$/", '', $pathNode);
                    /**
                     * Get the XML string representation from the found node, node tags are included,
                     * SimpleXMLElement object is created and returns a Generator
                     */
                    yield (new \SimpleXMLElement($this->reader->readOuterXML()));
                }
            }
        }
    }
}
