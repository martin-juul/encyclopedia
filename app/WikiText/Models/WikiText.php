<?php

namespace App\WikiText\Models;

class WikiText
{
    public int $id;
    public ?int $parent_id;
    public string $timestamp;
    public ?WikitextContributor $contributor;
    public ?string $comment;
    public ?string $text;
    public string $sha1;

    public function __construct(
        int $id,
        ?int $parent_id,
        string $timestamp,
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
