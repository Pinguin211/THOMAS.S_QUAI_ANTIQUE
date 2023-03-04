<?php

namespace App\Entity;

use App\Repository\BookerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: BookerRepository::class)]
class Booker
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\ManyToMany(targetEntity: Ingredient::class)]
    private Collection $allergys;

    #[ORM\OneToMany(mappedBy: 'booker', targetEntity: Reservation::class, orphanRemoval: true)]
    private Collection $reservations;

    public function __construct()
    {
        $this->allergys = new ArrayCollection();
        $this->reservations = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Ingredient>
     */
    public function getAllergys(): Collection
    {
        return $this->allergys;
    }

    public function addAllergy(Ingredient $allergy): self
    {
        if (!$this->allergys->contains($allergy)) {
            $this->allergys->add($allergy);
        }

        return $this;
    }

    public function removeAllergy(Ingredient $allergy): self
    {
        $this->allergys->removeElement($allergy);

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
            $reservation->setBooker($this);
        }

        return $this;
    }

    public function removeReservation(Reservation $reservation): self
    {
        if ($this->reservations->removeElement($reservation)) {
            // set the owning side to null (unless already changed)
            if ($reservation->getBooker() === $this) {
                $reservation->setBooker(null);
            }
        }

        return $this;
    }
}
