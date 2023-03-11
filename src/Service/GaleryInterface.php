<?php

namespace App\Service;


class GaleryInterface
{
    public function __construct(private CheckerInterface $checker,
                                private PathInterface $path)
    {
    }

    public function addGaleryImage(array $file): array | false
    {
        if (!$this->isGoodImage($file))
            return false;
        $name = time();
        $ext = $this->checker->fileGetExtension($file);
        if ($this->imageExist($name, $ext))
            $name++;
        $path = $this->path->getGaleryImageFilePath($name, $ext);
        if (file_exists($file['tmp_name']))
            move_uploaded_file($file['tmp_name'], $path);
        else
            return false;
        return ['name' => (string)$name, 'type' => $ext, 'path' => $path];
    }

    public function removeGaleryImage(string $name, string $ext_type): void
    {
        $path = $this->path->getGaleryImageFilePath($name, $ext_type);
        if (file_exists($path))
            unlink($path);
    }

    /**
     * IMAGE PNG ou JPG ou JPEG
     */
    public function isGoodImage(array $file, int $max_size = 2000000): bool
    {
        return $this->checker->checkUploadedFile($file, $max_size, ['png', 'jpg', 'jpeg'], ['image/png', 'image/jpeg', 'image/jpg']);
    }


    public function imageExist(string $name, string $ext_type): bool
    {
        return file_exists($this->path->getGaleryImageFilePath($name, $ext_type));
    }
}