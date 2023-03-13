<?php

namespace App\Controller;

use App\Service\AutomaticInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MessageController extends AbstractController
{
    #[Route('/message', name: 'app_message')]
    public function MessagePage(AutomaticInterface $auto): Response
    {
        $auto->setLoginForceRedirectRoute($_GET['redirect_app'] ?? 'app_homepage');
        return $this->render('message.html.twig', [
            'title' => $_GET['title'] ?? 'Erreur',
            'message' => $_GET['message'] ?? "Revenir à l'accueil",
            'redirect_app' => $_GET['redirect_app'] ?? 'app_homepage',
            'elem_ref' => isset($_GET['elem_ref']) ? '#'.$_GET['elem_ref'] : '',
            'auto' => $auto->getParams()
        ]);
    }
}
