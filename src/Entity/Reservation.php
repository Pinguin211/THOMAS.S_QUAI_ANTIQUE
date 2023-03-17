<?php

namespace App\Entity;

use App\Entity\Timetable\Timetable;
use App\Repository\ReservationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Service $service = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $cutlerys = null;

    #[ORM\Column]
    private ?bool $allergy = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $stage_hour = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Booker $booker = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getCutlerys(): ?int
    {
        return $this->cutlerys;
    }

    public function setCutlerys(int $cutlerys): self
    {
        $this->cutlerys = $cutlerys;

        return $this;
    }

    public function isAllergy(): ?bool
    {
        return $this->allergy;
    }

    public function setAllergy(bool $allergy): self
    {
        $this->allergy = $allergy;

        return $this;
    }

    public function getStageHour(): ?int
    {
        return $this->stage_hour;
    }

    public function setStageHour(int $stage_hour): self
    {
        $this->stage_hour = $stage_hour;

        return $this;
    }

    public function getBooker(): ?Booker
    {
        return $this->booker;
    }

    public function setBooker(?Booker $booker): self
    {
        $this->booker = $booker;

        return $this;
    }

    public function getAsArray(Timetable $timetable, int $service_type, \DateTime $dateTime): array
    {
        $allergys = $this->allergy ? $this->booker->getAllergysAsArrayName() : [];
        return [
            'id' => $this->id,
            'name' => $this->booker->getName(),
            'cutlerys' => $this->cutlerys,
            'allergys' => $allergys,
            'hour' => $timetable->getHourFromStage($this->stage_hour, $service_type, $dateTime)?->format('H:i')
        ];
    }
}
