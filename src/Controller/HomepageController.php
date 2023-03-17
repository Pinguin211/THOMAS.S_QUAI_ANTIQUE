<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Entity\GaleryImage;
use App\Service\AutomaticInterface;
use App\Service\InfoFileInterface;
use App\Service\ReservationInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends AbstractController
{
    private const CAROUSEL_LIMIT = 3;
    private const MENUS_LIMIT = 6;

    #[Route('/', name: 'app_homepage')]
    public function index(AutomaticInterface $auto, EntityManagerInterface $entityManager,
                          ReservationInterface $reservation, InfoFileInterface $infoFile): Response
    {

        return $this->render('homepage/index.html.twig', [
            'arr_img' => self::getCarouselArrImage($entityManager),
            'str_reservation' => $reservation->getStrReservation(),
            'time' => self::getTimeTable($infoFile),
            'menus' => self::getMenus($entityManager),
            'auto' => $auto->getParams()
        ]);
    }

    private static function getMenus(EntityManagerInterface $entityManager): array
    {
        $menus = $entityManager->getRepository(Dish::class)->findBy(['type' => [Dish::TYPE_ENTRES, Dish::TYPE_PLATS, Dish::TYPE_DESSERTS], 'archived' => false]);
        shuffle($menus);
        $arr = [];
        $i = 0;
        while (isset($menus[$i]) && $i < self::MENUS_LIMIT)
        {
            $arr[] = $menus[$i];
            $i++;
        }
        return $arr;
    }

    private static function getTimeTable(InfoFileInterface $infoFile)
    {
        if (!($timetable = $infoFile->getTimetable()))
            return ['date' => 'Erreur', 'arr' => []];
        $date = new \DateTime();
        $info = ['date' => $date->format('d/m/Y')];
        $titles = ["Aujourd'hui", "Demain", "Apres-demain"];
        foreach ($titles as $title)
        {
            if (($day = $timetable->getDayFromDateTime($date)))
            {
                $arr = [];
                $arr['title'] = $title;
                $arr['day'] = $day->getDaySessionString();
                $arr['night'] = $day->getNightSessionString();
                $info['arr'][] = $arr;
            }
            $date->modify('+1 day');
        }
        return $info;
    }

    private static function getCarouselArrImage(EntityManagerInterface $entityManager): array
    {
        $images = $entityManager->getRepository(GaleryImage::class)->findAll();
        shuffle($images);
        $arr_img = [];
        $i = 0;
        while (isset($images[$i]) && $i < self::CAROUSEL_LIMIT)
        {
            $img = [];
            $img['pos'] = $i;
            $img['path'] = $images[$i]->getPath();
            $img['title'] = $images[$i]->getTitle();
            $arr_img[] = $img;
            $i++;
        }
        return $arr_img;
    }
}
