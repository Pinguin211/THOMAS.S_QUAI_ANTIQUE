<?php

namespace App\Service;

use App\Entity\Service;
use App\Entity\Timetable\Day;
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
       $max_cutlerys = $this->infoFile->getCutlerys();
       $arr = [];
       $day_service = $this->getService(Service::DAY_KEY, $date);
       $night_service = $this->getService(Service::NIGHT_KEY, $date);
       if ($day->getDay() &&
           (($day_service === false && $max_cutlerys > 0) || ($day_service && $day_service->isComplet() === false)))
           $arr = array_merge(
               $arr,
               $this->getServiceArrayStage(Service::DAY_KEY, $date->format('Y-m-d'), $day->getDay())
           );
       if ($day->getNight() &&
           (($night_service === false && $max_cutlerys > 0) || ($night_service && $night_service->isComplet() === false)))
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

    public function freeReservationPlacesOnService(DateTime $dateTime, int $type): int
    {
        if (!($service = $this->getService($type, $dateTime)))
            return $this->infoFile->getCutlerys();
        else
            return $service->getFreeReservationCutlerys();
    }

    private function getNextReservationInfo(): array | false
    {
        $today = new \DateTime();
        $i = 0;
        $service = false;
        $sess = false;
        $timetable = $this->infoFile->getTimetable();
        while ((!$service || $service <= 0) && $i < 60 && $timetable) {
            if (($day = $timetable->getDayFromDateTime($today)))
            {
                if (!($sess = $day->getNextSessionDayFromDateTime($today)) ||
                    ($service = $this->freeReservationPlacesOnService($today, $sess->getType())) <= 0)
                {
                    if (($sess = $day->getNextSessionNightFromDateTime($today)))
                        $service = $this->freeReservationPlacesOnService($today, $sess->getType());
                }
            }
            $today->modify('+1 day');
            $today->setTime(0, 0);
            $i++;
        }
        if (!$service || $service <= 0 || $sess === false)
            return false;
        else
        {
            $today->modify('-1 day');
            $i--;
            return [
                'free_cutlerys' => $service,
                'session' => $sess,
                'date' => $today,
                'i' => $i,
            ];
        }

    }

    public function getStrReservation(): string
    {
        if (!($arr = $this->getNextReservationInfo()))
            return 'Il ny a pas de reservation disponible';
        else
        {
            $sess = $arr['session'];
            $i = $arr['i'];
            $today = $arr['date'];
            $service = $arr['free_cutlerys'];
            if ($sess->getType() === Day::KEY_TYPE_DAY)
                $session = 'midi';
            else
                $session = 'soir';
            $day = match ($i) {
                0 => "aujourd'hui",
                1 => "demain",
                2 => "apres-demain",
                default => 'le ' . $today->format('d/m/Y')
            };
            if ($service < 9)
                $cutlerys = 'une table restante';
            else
                $cutlerys = $service . ' couverts restants';
            return "Prochaine reservation disponible $day, sur le service du $session, $cutlerys";
        }
    }

    public function getNextDateReservation(): DateTime | false
    {
        if (!($arr = $this->getNextReservationInfo()))
            return false;
        else
        {
            $date = $arr['date'];
            $date->setTime(0, 0);
            return $date;
        }
    }
}