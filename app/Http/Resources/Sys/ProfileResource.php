<?php

namespace App\Http\Resources\Sys;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Class ProfileResource
 * @package App\Http\Resources\Sys
 * @mixin \App\Models\Sys\ProfileReport
 */
class ProfileResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id'       => $this->id,
            'category' => $this->category,
            'context'  => $this->context,
            'xhprof'   => $this->xhprof,
            'created'  => $this->created_at->toJSON(),
        ];
    }
}
