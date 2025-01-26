<?php

// src/Entity/Medico.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Medico extends User
{
    #[ORM\Column(type: 'string', length: 100)]
    private string $especialidad;

    public function getEspecialidad(): string
    {
        return $this->especialidad;
    }

    public function setEspecialidad(string $especialidad): self
    {
        $this->especialidad = $especialidad;
        return $this;
    }
}
