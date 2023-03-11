<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\User;
use App\Form\SignupType;
use App\Service\AutomaticInterface;
use App\Service\ClientInterface;
use App\Service\IngredientsInterface;
use App\Service\RolesInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class SignupController extends AbstractController
{
    #[Route('/signup', name: 'app_signup')]
    public function index(AutomaticInterface $auto, Request $request,
                          EntityManagerInterface $entityManager,
                          UserPasswordHasherInterface $hasher,
                          ClientInterface $clientInterface,
                          IngredientsInterface $ingredients): Response
    {
        if ($this->getUser())
            return $this->redirectToRoute('app_homepage');

        //Signup form
        $user = new User();
        $form = $this->createForm(SignupType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $ingredients = self::getSelectedIngredients($form->get('ingredients')->getData(), $entityManager);
            $cutlerys = $form->get('cutlerys')->getData();
            $name = explode('@', $user->getEmail())[0];
            $user->setPassword($hasher->hashPassword($user, $user->getPassword()));
            $user->setRoles([RolesInterface::ROLE_CLIENT]);
            $entityManager->persist($user);
            $entityManager->flush();
            $clientInterface->CreateClient($user, $cutlerys, $name, $ingredients);
            return $this->redirectToRoute('app_message', ['title' => 'Inscription réussie',
                'message' => "Vous pouvez maintenant vous connecter"]);
        }

        return $this->render('signup/index.html.twig', [
            'controller_name' => 'SignupController',
            'form' => $form->createView(),
            'ingredients_list' => $ingredients->getAllIngredientsListByType(),
            'ingredients_types' => Ingredient::TYPE_NAMES,
            'auto' => $auto->getParams()
        ]);
    }

    private static function getSelectedIngredients(array $ids, EntityManagerInterface $entityManager): array
    {
        $arr = [];
        foreach ($ids as $id)
        {
            if (is_numeric($id) &&
                ($ingr = $entityManager->getRepository(Ingredient::class)->findOneBy(['id' => $id])))
                $arr[] = $ingr;
        }
        return $arr;
    }
}
