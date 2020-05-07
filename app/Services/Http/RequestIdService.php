<?php

namespace App\Services\Http;

use Ramsey\Uuid\Uuid;

class RequestIdService
{
    private ?string $id = null;

    /**
     * Get the unique request id
     *
     * @return string|null
     */
    public function get(): ?string
    {
        if (!config('logging.request.enable')) {
            return null;
        }

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
