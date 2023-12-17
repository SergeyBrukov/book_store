<?php

namespace App\Services;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileServices
{
    private string $baseFolder = __DIR__;
    private string $storageWay = '/../storage';

    public function __construct
    (
        private readonly Filesystem $filesystem,
    )
    {
    }

    /**
     * @param mixed $data
     * @param string $fileName
     * @return void
     */
    public function saveFile(mixed $data, string $fileName): void
    {

        if (!$this->filesystem->exists('storage')) {
            $this->filesystem->mkdir('storage');
        }

        $this->filesystem->dumpFile($this->baseFolder . $this->storageWay . '/' . $fileName, $data);

    }

    public function saveImage(UploadedFile $image, string $fileName):void
    {
        if ($this->filesystem->exists('storage')) {
            $this->filesystem->mkdir('storage');
        }

        $image->move($this->baseFolder . $this->storageWay, $fileName);
    }
}