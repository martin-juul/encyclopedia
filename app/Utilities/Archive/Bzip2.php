<?php

namespace App\Utilities\Archive;

class Bzip2
{
    protected ?string $path;

    public function __construct(string $path = null)
    {
        if ($path !== null) {
            $this->setPath($path);
        }
    }

    public function getPath(): ?string
    {
        return $this->path;
    }

    public function setPath(string $path): void
    {
        if (!file_exists($path)) {
            throw new \RuntimeException("File does not exist at $path");
        }

        if (!is_readable($path)) {
            throw new \RuntimeException("File is not readable $path");
        }

        $this->path = $path;
    }

    /**
     * Open a bzip2 archive.
     * Mode can be either 'r' for reading or 'w' for writing.
     *
     * @param string $mode
     *
     * @return resource
     */
    public function open($mode = 'r')
    {
        $resource = bzopen($this->path, $mode);

        if ($resource === false) {
            throw new \RuntimeException("Could not open file at $this->path");
        }

        return $resource;
    }
}
