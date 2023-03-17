<?php

namespace App\Entity;

use App\Entity\Timetable\Timetable;
use App\Repository\ServiceRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ServiceRepository::class)]
class Service
{
    public const DAY_KEY = 1;
    public const NIGHT_KEY = 2;
    public const DAY_TYPE = 'day';
    public const NIGHT_TYPE = 'night';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $type = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $max_cutlerys = null;

    #[ORM\OneToMany(mappedBy: 'service', targetEntity: Reservation::class, orphanRemoval: true)]
    private Collection $reservations;

    public function __construct()
    {
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getMaxCutlerys(): ?int
    {
        return $this->max_cutlerys;
    }

    public function setMaxCutlerys(int $max_cutlerys): self
    {
        $this->max_cutlerys = $max_cutlerys;

        return $this;
    }

    /**
     * @return Collection<int, Reservation>
     */
    public function getReservations(): Collection
    {
        return $this->reservations;
    }

    public function addReservation(Reservation $reservation): self
    {
        if (!$this->reservations->contains($reservation)) {
            $this->reservations->add($reservation);
            $reservation->setService($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getService() === $this) {
                $reservation->setService(null);
            }
        }

        return $this;
    }

    public function isComplet(): bool
    {
        if ($this->getReservedCutlerys() >= $this->max_cutlerys)
            return true;
        else
            return false;
    }

    public function getReservedCutlerys(): int
    {
        $i = 0;
        foreach ($this->reservations as $reservation)
            $i += $reservation->getCutlerys();
        return $i;
    }

    public function getFreeReservationCutlerys(): int
    {
        return $this->getMaxCutlerys() - $this->getReservedCutlerys();
    }

    public function getAsArray(Timetable $timetable): array
    {
        $arr_reservations = [];
        $date = \DateTime::createFromFormat('Y-m-d H:i', $this->getDate()->format('Y-m-d H:i'));
        foreach ($this->getReservations() as $reservation)
            $arr_reservations[] = $reservation->getAsArray($timetable, $this->type, $date);
        usort($arr_reservations, function ($a, $b) {
            $a_arr = explode(':', $a['hour']);
            $ah = (int)$a_arr[0];
            $am = (int)$a_arr[1];
            $b_arr = explode(':', $b['hour']);
            $bh = (int)$b_arr[0];
            $bm = (int)$b_arr[1];
            if ($ah > $bh)
                return 1;
            elseif ($ah < $bh)
                return -1;
            else
            {
                if ($am > $bm)
                    return 1;
                elseif ($am < $bm)
                    return -1;
                else
                    return 0;
            }
        });
        return [
            'id' => $this->id,
            'type' => $this->type,
            'date' => $date->format('d/m/Y'),
            'reservations' => $arr_reservations,
            'max_cutlerys' => $this->getMaxCutlerys(),
            'reserved_cutlerys' => $this->getReservedCutlerys(),
        ];
    }
}
