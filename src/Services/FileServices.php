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

    /**
     * @param $fileFolder
     * @param UploadedFile $image
     * @param string $fileName
     * @param string $entityFolderName
     * @return void
     */
    public function saveImage($fileFolder, UploadedFile $image, string $fileName, string $entityFolderName):void
    {
        if (!$this->filesystem->exists('storage')) {
            $this->filesystem->mkdir('storage');
        }

        if (!$this->filesystem->exists('storage' . $entityFolderName)) {
            $this->filesystem->mkdir('storage' . '/' . $entityFolderName);
        }

        if (!$this->filesystem->exists('storage' . $fileFolder)) {
            $this->filesystem->mkdir('storage' . '/' . $entityFolderName . '/' . $fileFolder);
        }

        $image->move('storage' . '/' . $entityFolderName . '/' . $fileFolder, $fileName);
    }
}