<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\Ingredient;
use App\Entity\Menu;
use App\Entity\Timetable\Timetable;
use App\Form\DishType;
use App\Form\IngredientType;
use App\Form\MenuType;
use App\Form\TimetableType;
use App\Service\AutomaticInterface;
use App\Service\InfoFileInterface;
use App\Service\IngredientsInterface;
use App\Service\RolesInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin/timetable', name: 'app_timetable')]
    public function timetable(AutomaticInterface $automatic, Request $request, InfoFileInterface $file): Response
    {
        $timetable = $file->getTimetable();
        if ($timetable === false)
            $timetable = new Timetable([]);
        $form = $this->createForm(TimetableType::class, $timetable);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file->setTimetable($timetable);
            return $this->redirectToRoute('app_message', ['title' => 'Horaires mis à jours',
                'message' => "Les horaires on bien était mis à jours",
                'redirect_app' => 'app_timetable']);
        }
        return $this->render('admin/timetable.html.twig', [
            'form' => $form->createView(),
            'days_keys' => Timetable::ARR_KEY_DAYS,
            'auto' => $automatic->getParams()
        ]);
    }


    #[Route('/admin/dish', name: 'app_dish')]
    public function dish(AutomaticInterface $automatic, Request $request, IngredientsInterface $ingredients,
                         RolesInterface $roles, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser() || !($admin = $this->getUser()->getAdmin($roles, $entityManager)))
            return $this->redirectToRoute('app_homepage');

        if (isset($_GET['id']) &&
            ($dish = $entityManager->getRepository(Dish::class)->findOneBy(['id' => $_GET['id']])))
            $mod = true;
        else {
            $dish = new Dish();
            $mod = false;
        }
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            if ($mod) {
                $admin->flush();
                return $this->redirectToRoute('app_message', ['title' => 'Plats mis à jours',
                    'message' => "Le plat à bien était mis a jours",
                    'redirect_app' => 'app_menu',
                    'elem_ref' => 'dish'.$dish->getId()]);
            } else {
                $admin->addDish($dish);
                return $this->redirectToRoute('app_message', ['title' => 'Plats ajoutés',
                    'message' => "Le plat à bien était ajouté à la liste",
                    'redirect_app' => 'app_menu',
                    'elem_ref' => 'dish'.$dish->getId()]);
            }

        }
        return $this->render('admin/dish.html.twig', [
            'form' => $form->createView(),
            'ingredients_list' => $ingredients->getAllIngredientsListByType(),
            'ingredients_types' => Ingredient::TYPE_NAMES,
            'auto' => $automatic->getParams(),
            'opt' => $mod ? 'Modifier' : 'Ajouter',
        ]);
    }

    #[Route('/admin/ingredient', name: 'app_ingredient')]
    public function ingredient(AutomaticInterface $automatic, Request $request, IngredientsInterface $ingredients,
                               RolesInterface     $roles, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser() || !($admin = $this->getUser()->getAdmin($roles, $entityManager)))
            return $this->redirectToRoute('app_homepage');

        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            if ($ingredient->getType() >= 0 && $ingredient->getName())
                $admin->addIngredient($ingredient);
            foreach ($form->get('ingredients')->getData() as $id)
            {
                if (($ingredient = $entityManager->getRepository(Ingredient::class)->findOneBy(['id' => $id])))
                    $admin->deleteIngredient($ingredient);
            }
            return $this->redirectToRoute('app_ingredient');

        }
        return $this->render('admin/ingredient.html.twig', [
            'form' => $form->createView(),
            'ingredients_list' => $ingredients->getAllIngredientsListByType(),
            'ingredients_types' => Ingredient::TYPE_NAMES,
            'auto' => $automatic->getParams(),
        ]);
    }

    #[Route('/admin/menu', name: 'app_add_menu')]
    public function menu(AutomaticInterface $automatic, Request $request,
                         RolesInterface $roles, EntityManagerInterface $entityManager): Response
    {
        if (!$this->getUser() || !($admin = $this->getUser()->getAdmin($roles, $entityManager)))
            return $this->redirectToRoute('app_homepage');

        if (isset($_GET['id']) &&
            ($menu = $entityManager->getRepository(Menu::class)->findOneBy(['id' => $_GET['id']])))
            $mod = true;
        else {
            $menu = new Menu();
            $mod = false;
        }
        $form = $this->createForm(MenuType::class, $menu);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            if ($mod) {
                $admin->flush();
                return $this->redirectToRoute('app_message', ['title' => 'Menu mis à jours',
                    'message' => "Le menu à bien était mis a jours",
                    'redirect_app' => 'app_menu',
                    'elem_ref' => 'menu'.$menu->getId()]);
            } else {
                $admin->addMenu($menu);
                return $this->redirectToRoute('app_message', ['title' => 'Menu ajoutés',
                    'message' => "Le Menu à bien était ajouté à la liste",
                    'redirect_app' => 'app_menu',
                    'elem_ref' => 'menu'.$menu->getId()]);
            }
        }
        return $this->render('admin/menu.html.twig', [
            'form' => $form->createView(),
            'auto' => $automatic->getParams(),
            'opt' => $mod ? 'Modifier' : 'Ajouter',
        ]);
    }
}
