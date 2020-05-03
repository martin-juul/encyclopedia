<?php

namespace App\Services\Http;

use Ramsey\Uuid\Uuid;

class RequestIdService
{
    private ?string $id = null;

    /**
     * Get the unique request id
     *
     * @return string
     */
    public function get(): string
    {
        if (!$this->id) {
            $this->id = $this->generateId();
        }

        return $this->id;
    }

    private function generateId(): string
    {
        return Uuid::uuid6()->toString();
    }
}
