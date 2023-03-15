<?php

namespace App\Service;

use App\Entity\Service;
use App\Entity\Timetable\Session;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;

class ReservationInterface
{
    public function __construct(private EntityManagerInterface $entityManager,
                                private InfoFileInterface $infoFile)
    {
    }

   public function getArrayStagesFromDate(DateTime $date): array
   {
       if (!($timetable = $this->infoFile->getTimetable()) ||
           !($day = $timetable->getDayFromDateTime($date)))
           return [];
       $arr = [];
       if ($day->getDay() &&
           ($this->getService(Service::DAY_KEY, $date) === false ||
               $this->getService(Service::DAY_KEY, $date)->isComplet() === false))
           $arr = array_merge(
               $arr,
               $this->getServiceArrayStage(Service::DAY_KEY, $date->format('Y-m-d'), $day->getDay())
           );
       if ($day->getNight() &&
           ($this->getService(Service::NIGHT_KEY, $date) === false ||
               $this->getService(Service::NIGHT_KEY, $date)->isComplet() === false))
           $arr = array_merge(
               $arr,
               $this->getServiceArrayStage(Service::NIGHT_KEY, $date->format('Y-m-d'), $day->getNight())
           );
       return $arr;
   }

   public function getServiceArrayStage(int $type, string $date_string, Session $session, int $interval = 15, int $close_before_closed = 60): array
   {
       $start = DateTime::createFromFormat('Y-m-d H:i', "$date_string " . $session->getStart()->getMomentToString(':'));
       $now = new DateTime();
       if ($now < $start)
       {
           $end = DateTime::createFromFormat('Y-m-d H:i', "$date_string " . $session->getEnd()->getMomentToString(':'));
           $end->modify("-$close_before_closed minutes");
           $arr = [];
           $i = 0;
           while ($start <= $end) {
               $arr["$type.$i"] = $start->format('H') . "h" . $start->format('i');
               $i++;
               $start->modify("+$interval minutes");
           }
           return $arr;
       }
       else
           return [];
   }

   public function getService(int $type, DateTime $dateTime, bool $create_service = false, bool $flush = true): Service | false
   {
       $service = $this->entityManager->getRepository(Service::class)->findOneBy(['date' => $dateTime, 'type' => $type]);
       if ($service)
           return $service;
       else
       {
           if ($create_service)
               return $this->createService($dateTime, $type, $flush);
           else
               return false;
       }
   }

   public function createService(DateTime $dateTime, int $type, bool $flush = true): Service
   {
       $service = new Service();
       $service->setDate($dateTime);
       $service->setType($type);
       $service->setMaxCutlerys($this->infoFile->getCutlerys());
       $this->entityManager->persist($service);
       if ($flush)
           $this->entityManager->flush();
       return $service;
   }

   public function isValidStage(DateTime $dateTime, int $type, int $stage): bool
   {
       if (!($timetable = $this->infoFile->getTimetable()) ||
           !($day = $timetable->getDayFromDateTime($dateTime)) ||
           !($session = $day->getSessionByKeyType($type)))
           return false;
       $arr = $this->getServiceArrayStage($type, $dateTime->format('Y-m-d'), $session);
       if (!isset($arr["$type.$stage"]))
           return false;
       $service = $this->getService($type, $dateTime);
       if ($service && $service->isComplet())
           return false;
       return true;
   }
}