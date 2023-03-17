<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\Ingredient;
use App\Entity\Menu;
use App\Entity\Service;
use App\Entity\Timetable\Day;
use App\Entity\Timetable\Timetable;
use App\Entity\User;
use App\Form\DishType;
use App\Form\IngredientType;
use App\Form\MenuType;
use App\Form\TimetableType;
use App\Service\AutomaticInterface;
use App\Service\CheckerInterface;
use App\Service\InfoFileInterface;
use App\Service\IngredientsInterface;
use App\Service\InteractionInterface;
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
                         RolesInterface     $roles, EntityManagerInterface $entityManager): Response
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
                    'elem_ref' => 'dish' . $dish->getId()]);
            } else {
                $admin->addDish($dish);
                return $this->redirectToRoute('app_message', ['title' => 'Plats ajoutés',
                    'message' => "Le plat à bien était ajouté à la liste",
                    'redirect_app' => 'app_menu',
                    'elem_ref' => 'dish' . $dish->getId()]);
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
        if ($form->isSubmitted() && $form->isValid()) {
            if ($ingredient->getType() >= 0 && $ingredient->getName())
                $admin->addIngredient($ingredient);
            foreach ($form->get('ingredients')->getData() as $id) {
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
                         RolesInterface     $roles, EntityManagerInterface $entityManager): Response
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
                    'elem_ref' => 'menu' . $menu->getId()]);
            } else {
                $admin->addMenu($menu);
                return $this->redirectToRoute('app_message', ['title' => 'Menu ajoutés',
                    'message' => "Le Menu à bien était ajouté à la liste",
                    'redirect_app' => 'app_menu',
                    'elem_ref' => 'menu' . $menu->getId()]);
            }
        }
        return $this->render('admin/menu.html.twig', [
            'form' => $form->createView(),
            'auto' => $automatic->getParams(),
            'opt' => $mod ? 'Modifier' : 'Ajouter',
        ]);
    }

    #[Route('/admin/reservation', name: 'app_reservation_list')]
    public function reservation(AutomaticInterface $automatic, RolesInterface $roles, InfoFileInterface $infoFile): Response
    {
        if (!($user = $this->getUser()) || !($user instanceof User) || !$roles->is_admin($user))
            return $this->redirectToRoute('app_homepage');

        $today = new \DateTime();

        return $this->render('admin/reservation.html.twig', [
            'max_default_cutlerys' => $infoFile->getCutlerys(),
            'first_date' => $today->format('Y-m-d'),
            'auto' => $automatic->getParams()
        ]);
    }

    #[Route('/admin/set_default_cutlerys')]
    public function setDefaultCutlerys(InteractionInterface $interaction, InfoFileInterface $infoFile): Response
    {
        if (!($interaction->getAdmin($_POST)))
            return new Response('Bad id', 401);
        if (!($info = $interaction->getInfo($_POST, 'info', ['cutlerys'])) || !is_numeric($info['cutlerys']))
            return new Response('Mauvais paramètre, non mis à jours', 400);
        $infoFile->setCutlerys((int)$info['cutlerys']);
        return new Response('Nombre de couverts bien mis à jour', 200);
    }

    #[Route('/admin/get_services_reservations')]
    public function getServices(InteractionInterface $interaction, EntityManagerInterface $entityManager, InfoFileInterface $infoFile): Response
    {
        if (!($interaction->getAdmin($_POST)))
            return new Response('Bad id', 401);
        if (!($info = $interaction->getInfo($_POST, 'info', ['date'])) ||
            !($date = \DateTime::createFromFormat('Y-m-d', $info['date'])))
            return new Response('Mauvais paramètre, non mis à jours', 400);
        if (!($timetable = $infoFile->getTimetable()))
            return new Response('Initialiser les horaires pour voir les reservations', 400);

        $day_service = $entityManager->getRepository(Service::class)->findOneBy(['date' => $date, 'type' => Service::DAY_KEY]);
        $night_service = $entityManager->getRepository(Service::class)->findOneBy(['date' => $date, 'type' => Service::NIGHT_KEY]);

        $arr = [];
        $arr['day'] = $day_service ? $day_service->getAsArray($timetable) : false;
        $arr['night'] = $night_service ? $night_service->getAsArray($timetable) : false;

        return new Response(json_encode($arr));
    }


    #[Route('/admin/set_max_cutlerys_service')]
    public function setMaxCutlerysService(InteractionInterface $interaction,
                                          EntityManagerInterface $entityManager,
                                          CheckerInterface $checker): Response
    {
        if (!($interaction->getAdmin($_POST)))
            return new Response('Bad id', 401);
        if (!($info = $interaction->getInfo($_POST, 'info', ['date', 'cutlerys', 'type'])) ||
            !($date = \DateTime::createFromFormat('Y-m-d', $info['date'])) ||
            !($checker->checkDataWithAcceptedValue($info['type'], 'numeric', [Day::KEY_TYPE_DAY, Day::KEY_TYPE_NIGHT])) ||
            !($checker->checkData($info['cutlerys'], 'numeric')))
            return new Response('Mauvais paramètre, non mis à jours', 400);

        $today = new \DateTime();
        $today->setTime(0,0);
        if ($date < $today)
            return new Response('Vous ne pouvez pas modifier les services passés', 400);

        $service = $entityManager->getRepository(Service::class)->findOneBy(['date' => $date, 'type' => $info['type']]);
        if ($service)
            $service->setMaxCutlerys((int)$info['cutlerys']);
        else
        {
            $service = new Service();
            $service->setMaxCutlerys((int)$info['cutlerys']);
            $service->setType((int)$info['type']);
            $service->setDate($date);
            $entityManager->persist($service);
        }
        $entityManager->flush();
        return new Response('Nombre de couverts bien mis à jour pour ce service', 200);
    }
}
