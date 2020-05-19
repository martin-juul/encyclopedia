<?php
declare(strict_types=1);

namespace App\Pagination;

use Illuminate\Pagination\LengthAwarePaginator as BasePaginator;

class LengthAwarePaginator extends BasePaginator
{
    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'currentPage'  => $this->currentPage(),
            'data'         => $this->items->toArray(),
            'firstPageUrl' => $this->url(1),
            'from'         => $this->firstItem(),
            'lastPage'     => $this->lastPage(),
            'lastPageUrl'  => $this->url($this->lastPage()),
            'nextPageUrl'  => $this->nextPageUrl(),
            'path'         => $this->path(),
            'perPage'      => $this->perPage(),
            'prevPageUrl'  => $this->previousPageUrl(),
            'to'           => $this->lastItem(),
            'total'        => $this->total(),
        ];
    }

    public function toJson($options = 0)
    {
        return parent::toJson(JSON_UNESCAPED_SLASHES);
    }
}
