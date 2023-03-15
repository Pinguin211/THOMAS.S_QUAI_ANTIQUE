<?php

namespace App\Lib;

class JsonFile
{
    private string $path;
    private array $info;

    private function __construct(string $path, array $info)
    {
        $this->path = $path;
        $this->info = $info;
    }

    public static function ConstructWithPath(string $path, bool $create_file = true)
    {
        if (!file_exists($path) && !$create_file )
            return false;
        elseif (!file_exists($path))
            return new JsonFile($path, []);
        $file = json_decode(file_get_contents($path), true);
        if (is_array($file))
            return new JsonFile($path, $file);
        else
            return false;
    }

    public function fileExist(): bool
    {
        return file_exists($this->path);
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    public function keyExist(string $key): bool
    {
        return isset($this->info[$key]);
    }

    public function addInFile(string|int|array|float $value, string $key): bool
    {
        if ($this->keyExist($key))
            return false;
        else
            $this->setInFile($value, $key);
        return true;

    }

    public function setInFile(string|int|array|float $value, string $key): void
    {
        $this->info[$key] = $value;
    }

    public function getInFile(string $key): mixed
    {
        if ($this->keyExist($key))
            return $this->info[$key];
        else
            return NULL;
    }

    public function saveFile(): void
    {
        $json = json_encode($this->info, JSON_PRETTY_PRINT);
        file_put_contents($this->path, $json);
    }


}