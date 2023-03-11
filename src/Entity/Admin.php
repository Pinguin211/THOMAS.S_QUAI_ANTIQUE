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
        $this->entityManager->remove($galeryImage);
        $this->entityManager->flush();
    }

    public function setGaleryImageTitle(GaleryImage $galeryImage, string $title)
    {
        $galeryImage->setTitle($title);
        $this->entityManager->flush();
    }

    public function addDish(Dish $dish): void
    {
        $this->entityManager->persist($dish);
        $this->entityManager->flush();
    }

    public function flush(): void
    {
        $this->entityManager->flush();
    }
}
