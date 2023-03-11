<?php

namespace App\Controller;

use App\Entity\Timetable\Timetable;
use App\Form\TimetableType;
use App\Service\AutomaticInterface;
use App\Service\InfoFileInterface;
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
        if ($form->isSubmitted() && $form->isValid())
        {
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
}
