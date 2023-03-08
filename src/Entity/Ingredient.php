<?php

namespace App\Entity;

use App\Repository\IngredientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: IngredientRepository::class)]
class Ingredient
{
    public const TYPE_LEGUMES = 0;
    public const TYPE_FRUITS = 1;
    public const TYPE_FRUIT_A_COQUE = 2;
    public const TYPE_VIANDES = 3;
    public const TYPE_POISSONS = 4;
    public const TYPE_EPICES = 5;
    public const TYPE_FROMAGES = 6;
    public const TYPE_AUTRES = 7;

    public const TYPE_NAMES = [
        'Légumes', // 0
        'Fruits', // 1
        'Fruits à coque', // 2
        'Viandes', // 3
        'Poissons', // 4
        'Épices', // 5
        'Fromages', // 6
        'Autres' // 7
    ];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $type = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getTypeName(): string
    {
        return self::TYPE_NAMES[$this->type];
    }
}
