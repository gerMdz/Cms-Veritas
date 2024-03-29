<?php

namespace App\Service;

use Exception;
use Gedmo\Sluggable\Util\Urlizer;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Asset\Context\RequestStackContext;

//use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\Filesystem\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploaderHelper
{
    final public const IMAGE_ENTRADA = 'image_entrada';
    final public const ENTRADA_REFERENCE = 'entrada_reference';

    /**
     * UploaderHelper constructor.
     */
    public function __construct(private readonly Filesystem      $filesystem, private readonly RequestStackContext $context,
                                private readonly string          $uploadedAssetsBaseUrl, private readonly Filesystem $privateFilesystem,
                                private readonly LoggerInterface $logger)
    {
    }

    /**
     * @throws FilesystemException
     */
    public function uploadEntradaImage(File $file, ?string $existingFilename): string
    {
        $newFilename = $this->uploadFile($file, self::IMAGE_ENTRADA, true);

        if ($existingFilename) {
            try {
                $this->filesystem->delete(self::IMAGE_ENTRADA . '/' . $existingFilename);

            } catch (FileNotFoundException) {
                $this->logger->alert(sprintf('No se pudo borrar "%s" imagen perdida', $existingFilename));
            }
        }

        return $newFilename;
    }

    public function uploadEntradaReference(File $file): string
    {
        return $this->uploadFile($file, self::ENTRADA_REFERENCE, false);
    }

    public function getPublicPath(string $path): string
    {
        // needed if you deploy under a subdirectory
        return $this->context
                ->getBasePath() . $this->uploadedAssetsBaseUrl . '/' . $path;
    }

    private function uploadFile(File $file, string $directory, bool $isPublic): string
    {
        if ($file instanceof UploadedFile) {
            $originalFilename = $file->getClientOriginalName();
        } else {
            $originalFilename = $file->getFilename();
        }

        $newFilename = Urlizer::urlize(pathinfo($originalFilename, PATHINFO_FILENAME)) . '-' . uniqid() . '.' . $file->guessExtension();

        $filesystem = $isPublic ? $this->filesystem : $this->privateFilesystem;

        $stream = fopen($file->getPathname(), 'r');
        $result = $filesystem->writeStream(
            $directory . '/' . $newFilename,
            $stream
        );
        if (false === $result) {
            throw new Exception(sprintf('No se pudo escribir el archivo cargado "%s"', $newFilename));
        }
        if (is_resource($stream)) {
            fclose($stream);
        }

        return $newFilename;
    }

    public function readStream(string $path, bool $isPublic)
    {
        $filesystem = $isPublic ? $this->filesystem : $this->privateFilesystem;
        $resource = $filesystem->readStream($path);

        if (false === $resource) {
            throw new Exception(sprintf('Error al abrir secuencia para "%s"', $path));
        }

        return $resource;
    }

    /**
     * @throws FilesystemException
     * @throws Exception
     */
    public function deleteFile(string $path, bool $isPublic): void
    {
        $filesystem = $isPublic ? $this->filesystem : $this->privateFilesystem;
        try {
            $filesystem->delete($path);
        } catch (Exception $e) {
            throw new Exception(sprintf('Error al eliminar "%s": %s', $path, $e->getMessage()));
        }
    }
}
