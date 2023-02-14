<?php

 namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message:'Ce champ est obligatoire')]
    public ?string $Id_Rdv = null;
    
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank (message:'Ce champ est obligatoire')]
    private ?string $Docteur = null;

    #[ORM\Column(length: 255)]
    private ?string $Patient = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column(length: 255)]
    private ?string $adresse = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdRdv(): ?string
    {
        return $this->Id_Rdv;
    }

    public function setIdRdv(string $Id_Rdv): self
    {
        $this->Id_Rdv = $Id_Rdv;

        return $this;
    }

    public function getDocteur(): ?string
    {
        return $this->Docteur;
    }

    public function setDocteur(string $Docteur): self
    {
        $this->Docteur = $Docteur;

        return $this;
    }

    public function getPatient(): ?string
    {
        return $this->Patient;
    }

    public function setPatient(string $Patient): self
    {
        $this->Patient = $Patient;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getAdresse(): ?string
    {
        return $this->adresse;
    }

    public function setAdresse(string $adresse): self
    {
        $this->adresse = $adresse;

        return $this;
    }
}
