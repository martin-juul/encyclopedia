<?php

namespace App\WikiText\Models;

class WikitextContributor
{
    public int $id;
    public string $username;

    public function __construct(int $id, string $username)
    {
        $this->id = $id;
        $this->username = $username;
    }
}
