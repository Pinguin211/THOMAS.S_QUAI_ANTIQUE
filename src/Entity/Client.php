<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::SMALLINT)]
    private ?int $cutlerys = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?Booker $booker = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getBooker(): ?Booker
    {
        return $this->booker;
    }

    public function setBooker(Booker $booker): self
    {
        $this->booker = $booker;

        return $this;
    }
}
