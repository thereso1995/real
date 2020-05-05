<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\CompteRepository")
 */
class Compte
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list-compte","list-depot"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     *  @Groups({"list-entreprise","list-userCmpt","list-compte"})
     */
    private $numeroCompte;

    /**
     * @ORM\Column(type="bigint", nullable=true)
     * @Groups({"list-entreprise","list-compte","list-userCmpt"})
     */
    private $solde;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserCompteActuel", mappedBy="compte")
     */
    private $userCompteActuels;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Entreprise", inversedBy="comptes")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"list-compte"})
     */
    private $entreprise;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Depot", mappedBy="compte")
     */
    private $depots;

    public function __construct()
    {
        $this->userCompteActuels = new ArrayCollection();
        $this->depots = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumeroCompte(): ?string
    {
        return $this->numeroCompte;
    }

    public function setNumeroCompte(string $numeroCompte): self
    {
        $this->numeroCompte = $numeroCompte;

        return $this;
    }

    public function getSolde(): ?string
    {
        return $this->solde;
    }

    public function setSolde(?string $solde): self
    {
        $this->solde = $solde;

        return $this;
    }

    /**
     * @return Collection|UserCompteActuel[]
     */
    public function getUserCompteActuels(): Collection
    {
        return $this->userCompteActuels;
    }

    public function addUserCompteActuel(UserCompteActuel $userCompteActuel): self
    {
        if (!$this->userCompteActuels->contains($userCompteActuel)) {
            $this->userCompteActuels[] = $userCompteActuel;
            $userCompteActuel->setCompte($this);
        }

        return $this;
    }

    public function removeUserCompteActuel(UserCompteActuel $userCompteActuel): self
    {
        if ($this->userCompteActuels->contains($userCompteActuel)) {
            $this->userCompteActuels->removeElement($userCompteActuel);
            // set the owning side to null (unless already changed)
            if ($userCompteActuel->getCompte() === $this) {
                $userCompteActuel->setCompte(null);
            }
        }

        return $this;
    }

    public function getEntreprise(): ?Entreprise
    {
        return $this->entreprise;
    }

    public function setEntreprise(?Entreprise $entreprise): self
    {
        $this->entreprise = $entreprise;

        return $this;
    }

    /**
     * @return Collection|Depot[]
     */
    public function getDepots(): Collection
    {
        return $this->depots;
    }

    public function addDepot(Depot $depot): self
    {
        if (!$this->depots->contains($depot)) {
            $this->depots[] = $depot;
            $depot->setCompte($this);
        }

        return $this;
    }

    public function removeDepot(Depot $depot): self
    {
        if ($this->depots->contains($depot)) {
            $this->depots->removeElement($depot);
            // set the owning side to null (unless already changed)
            if ($depot->getCompte() === $this) {
                $depot->setCompte(null);
            }
        }

        return $this;
    }
}
