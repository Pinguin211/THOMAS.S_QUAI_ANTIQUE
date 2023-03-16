<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Entity\Reservation;
use App\Form\ReservationType;
use App\Service\AutomaticInterface;
use App\Service\CheckerInterface;
use App\Service\InfoFileInterface;
use App\Service\IngredientsInterface;
use App\Service\ReservationInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ReservationController extends AbstractController
{
    #[Route('/reservation', name: 'app_reservation')]
    public function index(AutomaticInterface $automatic, Request $request, InfoFileInterface $infoFile,
                          IngredientsInterface $ingredients, EntityManagerInterface $entityManager,
                          ReservationInterface $reservationInterface): Response
    {
        $reservation = new Reservation();
        if ($this->getUser())
        {
            $client = $this->getUser()->getClient();
            $reservation->setCutlerys($client->getCutlerys());
            $reservation->setBooker($client->getBooker());
        }
        $form = $this->createForm(ReservationType::class, $reservation);
        if (!($next_reservation_date = $reservationInterface->getNextDateReservation()))
            $next_reservation_date = new DateTime();
        $form->get('date')->setData($next_reservation_date);
        if ($this->getUser())
            $form->get('name')->setData($this->getUser()->getClient()->getBooker()->getName());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid())
        {
            $entityManager->persist($reservation);
            $entityManager->flush();
            $cutlerys = $reservation->getCutlerys();
            $name = $reservation->getBooker()->getName();
            $timetable = $infoFile->getTimetable();
            if ($timetable)
            {
                $date = $timetable->getHourFromStage(
                    $reservation->getStageHour(),
                    $reservation->getService()->getType(),
                    $reservation->getService()->getDate(),
                );
                if ($date)
                    $date = ' le ' . $date->format('d/m/Y') . ' à ' . $date->format('H:i') . ',';
                else
                    $date = '';
            }
            else
                $date = '';
            return $this->redirectToRoute('app_message', [
                'title' => 'Reservation enregistré',
                'message' => "Au nom de $name,$date pour $cutlerys couverts. Veuillez respecter les horaires choisis sous peine d'annulation de la reservation",
                'redirect_app' => 'app_menu']);
        }

        return $this->render('reservation/index.html.twig', [
            'form' => $form->createView(),
            'ingredients_list' => $ingredients->getAllIngredientsListByType(),
            'ingredients_types' => Ingredient::TYPE_NAMES,
            'auto' => $automatic->getParams()
        ]);
    }


    #[Route('/reservation/get_stage')]
    public function getStage(CheckerInterface $checker, ReservationInterface $reservation): Response
    {
        if (!$checker->checkArrayData($_POST, 'date', 'string') ||
            !($date = DateTime::createFromFormat('Y-m-d', $_POST['date'])))
            return new Response('Erreur args', 400);
        $date->setTime(0, 0);
        $today =  new DateTime();
        $today->setTime(0, 0);
        if ($date < $today)
            return new Response('Vous ne pouvez pas réservez dans le passé', '400');
        $month = new DateTime();
        $month->setTime(0, 0);
        $month->modify('+2 month');
        if ($date > $month)
            return new Response('Vous pouvez réservez au maximum dans deux mois', '400');
        return new Response(json_encode($reservation->getArrayStagesFromDate($date)));
    }


    #[Route('/reservation/good_stage')]
    public function goodStage(CheckerInterface $checker, ReservationInterface $reservation): Response
    {
        if (!$checker->checkData($_POST, 'array', ['date', 'stage']) ||
            !$checker->checkArrayData($_POST, 'date', 'string') ||
            !$checker->checkArrayData($_POST, 'stage', 'string') ||
            !(count(($stage = explode('.', $_POST['stage']))) === 2) ||
            !($date = DateTime::createFromFormat('Y-m-d', $_POST['date'])))
            return new Response('Erreur args', 400);
        $date->setTime(0, 0);
        if (!$reservation->isValidStage($date, (int)$stage[0], (int)$stage[1]))
            return new Response('Horaire non disponible', 400);
        else
            return new Response('hee', 200);
    }
}
