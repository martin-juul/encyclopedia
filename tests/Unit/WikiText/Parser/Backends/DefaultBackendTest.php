<?php

namespace Tests\Unit\WikiText\Parser\Backends;

use App\WikiText\Parser\Backends\DefaultBackend;
use PHPUnit\Framework\TestCase;

class DefaultBackendTest extends TestCase
{
    private $backend;

    protected function setUp(): void
    {
        $this->backend = new DefaultBackend;
    }

    public function testEncapsulateElement()
    {
        $this->assertEquals('text', $this->backend->encapsulateElement('NO_FUNCTION', 'text'));
        $this->assertEquals('<b>text</b>', $this->backend->encapsulateElement('bold', 'text'));
    }

    public function testEncapsulateBold()
    {
        $this->assertEquals('<b>text</b>', $this->backend->encapsulateBold('text'));
    }

    public function testEncapsulateItalic()
    {
        $this->assertEquals('<i>text</i>', $this->backend->encapsulateItalic('text'));
    }

    public function testEncapsulateParagraph()
    {
        $this->assertEquals('<p>text</p>' . PHP_EOL, $this->backend->encapsulateParagraph('text'));
    }
}
