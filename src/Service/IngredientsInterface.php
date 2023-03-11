<?php

namespace App\Service;


use App\Entity\Ingredient;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class IngredientsInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function getIgredientsListFromType(int $type): array
    {
        return $this->entityManager->getRepository(Ingredient::class)->findBy(['type' => $type]);
    }

    public function getAllIngredientsListByType(): array
    {
        $arr = [];
        $i = 0;
        while ($i < count(Ingredient::TYPE_NAMES))
        {
            $arr[$i] = $this->getIgredientsListFromType($i);
            $i++;
        }
        return $arr;
    }

    public function getAllIngredientsOrderById(string $order = 'asc'): array
    {
        return $this->entityManager->getRepository(Ingredient::class)->findBy([], ['id' => strtoupper($order)]);
    }

    public function getIngredientArrayChoice(): array
    {
        $arr = [];
        foreach ($this->getAllIngredientsOrderById() as $ingredient)
            $arr[$ingredient->getName() . ' ✕'] = $ingredient->getId();
        return $arr;
    }

    public function getSelectedIngredients(array $ids): array
    {
        $arr = [];
        foreach ($ids as $id)
        {
            if (is_numeric($id) &&
                ($ingr = $this->entityManager->getRepository(Ingredient::class)->findOneBy(['id' => $id])))
                $arr[] = $ingr;
        }
        return $arr;
    }
}