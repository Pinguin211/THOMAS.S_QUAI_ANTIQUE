<?php

namespace App\Entity;

use App\Repository\DishRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

#[ORM\Entity(repositoryClass: DishRepository::class)]
class Dish
{

    public const TYPE_ENTRES = 0;
    public const TYPE_PLATS = 1;
    public const TYPE_DESSERTS= 2;
    public const TYPE_FORMULES = 3;
    public const ARRAY_TYPE = [self::TYPE_ENTRES, self::TYPE_PLATS, self::TYPE_DESSERTS, self::TYPE_FORMULES];


    public const KEY_ENTRES = 'Entrés';
    public const KEY_PLATS = 'Plats';
    public const KEY_DESSERTS = 'Desserts';
    public const KEY_FORMULES = 'Formules';
    public const ARRAY_KEY = [self::KEY_ENTRES, self::KEY_PLATS, self::KEY_DESSERTS, self::KEY_FORMULES];

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Length(max: 32, maxMessage: "Le titre doit contenir au maximum 32 caractères")]
    #[NotBlank]
    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[NotBlank]
    #[Length(max: 255, maxMessage: "La description doit contenir au maximum 255 caractères")]
    #[ORM\Column(length: 255)]
    private ?string $description = null;

    #[ORM\Column]
    private ?int $price = null;

    #[ORM\Column]
    private ?bool $archived = null;

    #[NotBlank]
    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $type = null;

    #[ORM\ManyToMany(targetEntity: Ingredient::class)]
    private Collection $ingredients;

    public function __construct()
    {
        $this->ingredients = new ArrayCollection();
    }

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function isArchived(): ?bool
    {
        return $this->archived;
    }

    public function setArchived(bool $archived): self
    {
        $this->archived = $archived;

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

    /**
     * @return Collection<int, Ingredient>
     */
    public function getIngredients(): Collection
    {
        return $this->ingredients;
    }

    public function addIngredient(Ingredient $ingredient): self
    {
        if (!$this->ingredients->contains($ingredient)) {
            $this->ingredients->add($ingredient);
        }

        return $this;
    }

    public function removeIngredient(Ingredient $ingredient): self
    {
        $this->ingredients->removeElement($ingredient);

        return $this;
    }

    public function setIngredientsByArray(array $ingredients): void
    {
        $this->ingredients = new ArrayCollection($ingredients);
    }

    public static function getDishTypeNameById(int $id): string
    {
        return match ($id)
        {
            self::TYPE_ENTRES => self::KEY_ENTRES,
            self::TYPE_PLATS => self::KEY_PLATS,
            self::TYPE_DESSERTS => self::KEY_DESSERTS,
            self::TYPE_FORMULES => self::KEY_FORMULES,
            default => 'Erreur'
        };
    }
}
