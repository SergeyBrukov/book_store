<?php

namespace App\Entity;

use App\Repository\MediaFilesRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: MediaFilesRepository::class)]
class MediaFiles
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['info-item:book'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['info-item:book'])]
    private ?string $folder = null;

    #[ORM\Column(length: 255)]
    #[Groups(['info-item:book'])]
    private ?string $filePath = null;

    #[ORM\Column(length: 255)]
    #[Groups(['info-item:book'])]
    private ?string $fileName = null;

    #[ORM\Column]
    #[Groups(['info-item:book'])]
    private ?int $fileSize = null;

    #[ORM\Column(length: 255)]
    #[Groups(['info-item:book'])]
    private ?string $fileFormat = null;

    #[ORM\ManyToOne(inversedBy: 'images')]
    private ?Book $book = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFolder(): ?string
    {
        return $this->folder;
    }

    public function setFolder(string $folder): static
    {
        $this->folder = $folder;

        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getFileName(): ?string
    {
        return $this->fileName;
    }

    public function setFileName(string $fileName): static
    {
        $this->fileName = $fileName;

        return $this;
    }

    public function getFileSize(): ?int
    {
        return $this->fileSize;
    }

    public function setFileSize(int $fileSize): static
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    public function getFileFormat(): ?string
    {
        return $this->fileFormat;
    }

    public function setFileFormat(string $fileFormat): static
    {
        $this->fileFormat = $fileFormat;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): static
    {
        $this->book = $book;

        return $this;
    }
}
