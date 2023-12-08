<?php

namespace App\Services;

use Symfony\Component\Filesystem\Filesystem;

class FileServices
{
    private string $baseFolder = __DIR__;
    private string $storageWay = '/../storage';

    public function __construct
    (
        private Filesystem $filesystem,
    )
    {
    }

    public function saveFile(mixed $data, string $fileName): void
    {

        if ($this->filesystem->exists('storage')) {
            $this->filesystem->mkdir('storage');
        }

        $this->filesystem->dumpFile($this->baseFolder . $this->storageWay . '/' . $fileName, $data);

    }
}