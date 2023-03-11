<?php

namespace App\Entity;

use App\Repository\GaleryImageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: GaleryImageRepository::class)]
class GaleryImage
{
    public const TYPE_NAME_JPEG = 'jpeg';
    public const TYPE_NAME_JPG = 'jpg';
    public const TYPE_NAME_PNG = 'png';

    public const TYPE_INT_JPEG = 1;
    public const TYPE_INT_JPG = 2;
    public const TYPE_INT_PNG = 3;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $type = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(int $type): self | false
    {
        if (!self::getTypeNameByInt($type))
            return false;
        $this->type = $type;

        return $this;
    }

    public function getTypeString(): string
    {
        return self::getTypeNameByInt($this->type);
    }

    public function setTypeByString(string $type): bool
    {
        $int_type = self::getTypeIntByName($type);
        if (!$int_type)
            return false;
        $this->type = $int_type;
        return true;
    }

    public function getPath(): string
    {
        return '/images/galery/' . $this->name . '.' . $this->getTypeString();
    }

    public static function getTypeNameByInt(int $type): string|false
    {
        return match ($type) {
            self::TYPE_INT_JPEG => self::TYPE_NAME_JPEG,
            self::TYPE_INT_JPG => self::TYPE_NAME_JPG,
            self::TYPE_INT_PNG => self::TYPE_NAME_PNG,
            default => false
        };
    }

    public static function getTypeIntByName(string $type): string|false
    {
        return match ($type) {
            self::TYPE_NAME_JPEG => self::TYPE_INT_JPEG,
            self::TYPE_NAME_JPG => self::TYPE_INT_JPG,
            self::TYPE_NAME_PNG => self::TYPE_INT_PNG,
            default => false
        };
    }
}
