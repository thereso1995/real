<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\TarifsRepository")
 */
class Tarifs
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $borneInferieure;

    /**
     * @ORM\Column(type="integer")
     */
    private $borneSuperieure;

    /**
     * @ORM\Column(type="integer")
     */
    private $valeur;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorneInferieure(): ?int
    {
        return $this->borneInferieure;
    }

    public function setBorneInferieure(int $borneInferieure): self
    {
        $this->borneInferieure = $borneInferieure;

        return $this;
    }

    public function getBorneSuperieure(): ?int
    {
        return $this->borneSuperieure;
    }

    public function setBorneSuperieure(int $borneSuperieure): self
    {
        $this->borneSuperieure = $borneSuperieure;

        return $this;
    }

    public function getValeur(): ?int
    {
        return $this->valeur;
    }

    public function setValeur(int $valeur): self
    {
        $this->valeur = $valeur;

        return $this;
    }
}
