<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\GaleryImage;
use App\Entity\User;
use App\Service\AutomaticInterface;
use App\Service\CheckerInterface;
use App\Service\GaleryInterface;
use App\Service\RolesInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GaleryController extends AbstractController
{
    #[Route('/galery', name: 'app_galery')]
    public function index(AutomaticInterface $automatic,
                          EntityManagerInterface $entityManager): Response
    {
        $images = $entityManager->getRepository(GaleryImage::class)->findBy([], ['id' => 'DESC']);
        return $this->render('galery/index.html.twig', [
            'images' => $images,
            'auto' => $automatic->getParams(),
        ]);
    }

    #[Route('/galery/add_image', name: 'app_add_image')]
    public function addImage(GaleryInterface $galery, CheckerInterface $checker,
                             RolesInterface $roles, EntityManagerInterface $entityManager) {
        $user = $this->getUser();
        if (!($user instanceof User) || !($admin = $user->getAdmin($roles, $entityManager)))
            return $this->redirectToRoute('app_galery');
        if (!$checker->checkArrayData($_POST, 'title', 'string') ||
            !$checker->checkArrayData($_FILES, 'image', 'array') ||
            !$admin->addGaleryImage($galery, $_FILES['image'], $_POST['title']))
            return $this->redirectToRoute('app_message', ['title' => 'Erreur Mauvais format',
                'message' => "Il semblerait que votre image ne convient pas aux exigences",
                'redirect_app' => 'app_galery']);
        else
            return $this->redirectToRoute('app_message', ['title' => 'image bien ajouté !!',
                'message' => "Elle est maintenant visible dans la galerie",
                'redirect_app' => 'app_galery']);

    }

    #[Route('/galery/delete_image', name: 'app_delete_image')]
    public function deleteImage(GaleryInterface $galery, CheckerInterface $checker,
                             RolesInterface $roles, EntityManagerInterface $entityManager): Response
    {
        if (!($admin = self::getAdmin($checker, $entityManager, $roles)))
            return new Response('Bad id', 401);
        if (!($info = self::getInfo($checker, $_POST['info'], ['image'])))
            return new Response('Bad parameter', 400);
        if (!($image = $entityManager->getRepository(GaleryImage::class)->findOneBy(['id'=>$info['image']])))
            return new Response('Image not exist', 400);
        $admin->removeGaleryImage($image, $galery);
        return new Response('Image correctement supprimer', 200);
    }


    #[Route('/galery/set_title_image', name: 'app_set_title_image')]
    public function setTitleImage(GaleryInterface $galery, CheckerInterface $checker,
                                RolesInterface $roles, EntityManagerInterface $entityManager): Response
    {
        if (!($admin = self::getAdmin($checker, $entityManager, $roles)))
            return new Response('Bad id', 401);
        if (!($info = self::getInfo($checker, $_POST['info'], ['image', 'title'])))
            return new Response('Bad parameter', 400);
        if (!($image = $entityManager->getRepository(GaleryImage::class)->findOneBy(['id' => $info['image']])))
            return new Response('Image not exist', 400);
        if (strlen($info['title']) < 1 || strlen($info['title']) > 32)
            return new Response('Bad format', 400);
        if ($image->getTitle() === $info['title'])
            return new Response('Le titre est à jours', 200);
        $admin->setGaleryImageTitle($image, $info['title']);
        return new Response('Titre correctement modifier', 200);
    }

    private static function getAdmin(CheckerInterface $checker, EntityManagerInterface $entityManager,
                                     RolesInterface $roles): Admin | false
    {
        if (!$checker->checkData($_POST, 'array', ['id', 'password', 'info']) ||
            !($user = $entityManager->getRepository(User::class)->findOneBy(['id' => $_POST['id']])) ||
            $user->getPassword() !== $_POST['password'] ||
            !($admin = $user->getAdmin($roles, $entityManager)))
            return false;
        else
            return $admin;
    }


    private static function getInfo(CheckerInterface $checker, string $info, array $expected_value): array | false
    {
        $info = json_decode($_POST['info'], true);
        if (!$checker->checkData($info, 'array', $expected_value))
            return false;
        else
            return $info;
    }
}
