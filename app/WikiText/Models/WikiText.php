<?php
declare(strict_types=1);

namespace App\WikiText\Models;

class WikiText
{
    public $id;
    public $parent_id;
    public $timestamp;
    public ?WikitextContributor $contributor;
    public ?string $comment;
    public ?string $text;
    public string $sha1;

    public function __construct(
        $id,
        $parent_id,
        $timestamp,
        ?WikitextContributor $contributor,
        ?string $comment,
        ?string $text,
        string $sha1)
    {
        $this->id = $id;
        $this->parent_id = $parent_id;
        $this->timestamp = $timestamp;
        $this->contributor = $contributor;
        $this->comment = $comment;
        $this->text = $text;
        $this->sha1 = $sha1;
    }
}
