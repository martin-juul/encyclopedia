<?php
declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\Article
 */
class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'           => $this->article_id,
            'parentId'     => $this->article_parent_id,
            'title'        => $this->title,
            $this->mergeWhen(property_exists($this, 'text'), [
                'text' => $this->text,
            ]),
            'description'  => $this->description,
            'revisionTime' => $this->revision_time,
        ];
    }
}
