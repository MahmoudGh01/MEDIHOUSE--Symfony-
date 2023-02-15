<?php

namespace App\Entity;

use App\Repository\FicheRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheRepository::class)]
class Fiche
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $Id_Patient = null;

    #[ORM\Column(nullable: true)]
    private ?int $age = null;

    #[ORM\Column(type: Types::ARRAY, nullable: true)]
    private array $RendezVous = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPatient(): ?string
    {
        return $this->Id_Patient;
    }

    public function setIdPatient(string $Id_Patient): self
    {
        $this->Id_Patient = $Id_Patient;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getRendezVous(): array
    {
        return $this->RendezVous;
    }

    public function setRendezVous(?array $RendezVous): self
    {
        $this->RendezVous = $RendezVous;

        return $this;
    }
}
