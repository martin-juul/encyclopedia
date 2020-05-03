<?php

namespace App\WikiText\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;

class WikiPage implements Arrayable
{
    public string $title;
    public int $ns;
    public int $id;
    public ?string $redirectTitle;
    public WikiText $wikiText;

    public function __construct(\SimpleXMLElement $element)
    {
        $node = (array)$element;

        $this->title = Arr::get($node, 'title');
        $this->ns = (int)Arr::get($node, 'ns');
        $this->id = (int)Arr::get($node, 'id');
        $this->setRedirectTitle((array)Arr::get($node, 'redirect'));
        $this->setWikiText((array)$node['revision']);
    }

    public function toArray(): array
    {
        return [
            'title'    => $this->title,
            'ns'       => $this->ns,
            'id'       => $this->id,
            'redirect' => $this->redirectTitle,
            'revision' => [
                'id'              => $this->wikiText->id,
                'parent_id'       => $this->wikiText->parent_id,
                'sha1'            => $this->wikiText->sha1,
                'timestring_zulu' => $this->wikiText->timestamp,
                'contributor'     => [
                    'id'       => $this->wikiText->contributor->id,
                    'username' => $this->wikiText->contributor->username,
                ],
                'comment'         => $this->wikiText->comment,
                'text'            => $this->wikiText->text,
            ],
        ];
    }

    protected function setRedirectTitle(array $redirectTitle): void
    {
        if (!isset($redirectTitle['@attributes']['title'])) {
            $this->redirectTitle = null;
            return;
        }

        $this->redirectTitle = $redirectTitle['@attributes']['title'];
    }


    protected function setWikiText(array $wikiText): void
    {
        [
            'id'          => $id,
            'timestamp'   => $timestamp,
            'contributor' => $contributor,
            'text'        => $text,
            'sha1'        => $sha1,
        ] = $wikiText;

        isset($wikiText['comment']) ? $comment = $wikiText['comment'] : $comment = null;
        isset($wikiText['parentid']) ? $parent_id = $wikiText['parentid'] : $parent_id = null;

        $contributor = (array)$contributor;
        if (isset($contributor['id'])) {
            $contributor = new WikitextContributor((int)$contributor['id'], $contributor['username']);
        } else {
            $contributor = null;
        }

        $text = (array)$text;
        $text = $text['0'];

        $this->wikiText = new WikiText($id, $parent_id, $timestamp, $contributor, $comment, $text, $sha1);
    }
}
