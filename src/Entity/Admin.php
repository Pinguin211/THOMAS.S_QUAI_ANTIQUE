<?php

namespace App\Entity;

use App\Service\GaleryInterface;
use Doctrine\ORM\EntityManagerInterface;

class Admin
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function addGaleryImage(GaleryInterface $galery, array $file, string $title): bool
    {
         if (!($arr = $galery->addGaleryImage($file)))
             return false;
         $image = new GaleryImage();
         if (!$image->setTypeByString($arr['type']))
         {
             if (file_exists($arr['path']))
                 unlink($arr['path']);
             return false;
         }
         $image->setName($arr['name'])->setTitle($title);
         $this->entityManager->persist($image);
         $this->entityManager->flush();
         return true;
    }

    public function removeGaleryImage(GaleryImage $galeryImage, GaleryInterface $galery): void
    {
        $galery->removeGaleryImage($galeryImage->getName(), $galeryImage->getTypeString());
        $this->deleteObject($galeryImage);
    }

    public function setGaleryImageTitle(GaleryImage $galeryImage, string $title)
    {
        $galeryImage->setTitle($title);
        $this->flush();
    }

    public function addDish(Dish $dish): void
    {
        $this->addObject($dish);
    }

    public function addIngredient(Ingredient $ingredient)
    {
        $this->addObject($ingredient);
    }

    public function addMenu(Menu $menu)
    {
        $this->addObject($menu);
    }

    public function addObject(Mixed $obj): void
    {
        $this->entityManager->persist($obj);
        $this->flush();
    }

    public function deleteIngredient(Ingredient $ingredient): void
    {
        $this->deleteObject($ingredient);
    }

    public function deleteDish(Dish $dish): void
    {
        $this->deleteObject($dish);
    }

    public function deleteMenu(Menu $menu): void
    {
        $this->deleteObject($menu);
    }

    public function deleteObject(mixed $obj): void
    {
        $this->entityManager->remove($obj);
        $this->flush();
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
