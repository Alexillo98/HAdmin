<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\PacienteRepository;

#[ORM\Entity(repositoryClass: PacienteRepository::class)]
class Paciente extends User
{

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $historiaClinica = null;

    public function getHistoriaClinica(): ?string
    {
        return $this->historiaClinica;
    }

    public function setHistoriaClinica(?string $historiaClinica): self
    {
        $this->historiaClinica = $historiaClinica;
        return $this;
    }
}
