<?php

namespace App\Services;

use Dompdf\Dompdf;
use Symfony\Component\Filesystem\Filesystem;

class PdfGeneratorService
{
    private Dompdf $dompdf;
    private Filesystem $filesystem;
    private string $baseFolder = __DIR__;
    private string $storageWay = '/../storage';

    /**
     * @param Dompdf $dompdf
     * @param Filesystem $filesystem
     */
    public function __construct
    (
        Dompdf $dompdf,
        Filesystem $filesystem
    )
    {
        $this->dompdf = $dompdf;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $content
     * @param string $filePath
     * @return void
     */
    public function generateAndSavePdf(string $content, string $filePath): void
    {

        if (!$this->filesystem->exists('storage')) {
            $this->filesystem->mkdir('storage');
        }

        $this->dompdf->loadHtml($content);
        $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->render();

        $pdfContent = $this->dompdf->output();

        $this->filesystem->dumpFile($this->baseFolder . $this->storageWay . '/' . $filePath, $pdfContent);
    }
}