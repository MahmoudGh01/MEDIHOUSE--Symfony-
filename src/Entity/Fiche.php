<?php

namespace App\Entity;

use App\Repository\FicheRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: FicheRepository::class)]
class Fiche
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    public ?int $id = null;

    #[ORM\Column(length: 255)]
    public ?string $Id_Patient = null;

    #[ORM\Column(nullable: true)]
    public ?int $age = null;

    

    #[ORM\OneToMany(mappedBy: 'fiche', targetEntity: RendezVous::class)]
    private Collection $rendezVouses;

    

    public function __construct()
    {
        $this->rendezVouses = new ArrayCollection();
    }

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

   

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVouses(): Collection
    {
        return $this->rendezVouses;
    }

    public function addRendezVouse(RendezVous $rendezVouse): self
    {
        if (!$this->rendezVouses->contains($rendezVouse)) {
            $this->rendezVouses->add($rendezVouse);
            $rendezVouse->setFiche($this);
        }

        return $this;
    }

    public function removeRendezVouse(RendezVous $rendezVouse): self
    {
        if ($this->rendezVouses->removeElement($rendezVouse)) {
            // set the owning side to null (unless already changed)
            if ($rendezVouse->getFiche() === $this) {
                $rendezVouse->setFiche(null);
            }
        }

        return $this;
    }

    public function getPatient(): ?User
    {
        return $this->Patient;
    }

    public function setPatient(User $Patient): self
    {
        $this->Patient = $Patient;

        return $this;
    }
}
