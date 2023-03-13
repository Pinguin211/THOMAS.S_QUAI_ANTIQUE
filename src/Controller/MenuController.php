<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\Ingredient;
use App\Entity\Menu;
use App\Service\AutomaticInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MenuController extends AbstractController
{
    #[Route('/menu', name: 'app_menu')]
    public function index(AutomaticInterface $automatic, EntityManagerInterface $entityManager): Response
    {
        $dishes = [];
        foreach (Dish::ARRAY_TYPE as $type)
        {
            if ($type != Dish::TYPE_FORMULES)
            {
                $dishes[Dish::getDishTypeNameById($type)] =
                    $entityManager->getRepository(Dish::class)->findBy(['type' => $type, 'archived' => false]);
            }
        }
        $menus = $entityManager->getRepository(Menu::class)->findBy(['archived' => false]);

        $archived_dishes = $entityManager->getRepository(Dish::class)->findBy(['archived' => true], ['type' => 'ASC']);
        $archived_menus = $entityManager->getRepository(Menu::class)->findBy(['archived' => true]);

        return $this->render('menu/index.html.twig', [
            'dishes' => $dishes,
            'dishTypes' => array_diff(Dish::ARRAY_KEY,[Dish::KEY_FORMULES]),
            'ingredientTypes' => Ingredient::TYPE_NAMES,
            'menus' => $menus,
            'archived_dishes' => $archived_dishes,
            'archived_menus' => $archived_menus,
            'auto' => $automatic->getParams()
        ]);
    }
}
