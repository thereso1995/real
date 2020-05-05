<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Annotation\Groups;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass="App\Repository\EntrepriseRepository")
 */
class Entreprise
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"list-entreprise","list-compte","list-user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list-entreprise"})
     */
    private $raisonSociale;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list-entreprise"})
     */
    private $ninea;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list-entreprise"})
     */
    private $adresse;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"list-entreprise"})
     */
    private $status;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"list-entreprise"})
     */
    private $telephoneEntreprise;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"list-entreprise"})
     */
    private $emailEntreprise;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Utilisateur", mappedBy="entreprise")
     */
    private $utilisateurs;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Compte", mappedBy="entreprise")
     */
    private $comptes;

    public function __construct()
    {
        $this->utilisateurs = new ArrayCollection();
        $this->comptes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRaisonSociale(): ?string
    {
        return $this->raisonSociale;
    }

    public function setRaisonSociale(string $raisonSociale): self
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    public function getNinea(): ?string
    {
        return $this->ninea;
    }

    public function setNinea(string $ninea): self
    {
        $this->ninea = $ninea;

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

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getTelephoneEntreprise(): ?string
    {
        return $this->telephoneEntreprise;
    }

    public function setTelephoneEntreprise(?string $telephoneEntreprise): self
    {
        $this->telephoneEntreprise = $telephoneEntreprise;

        return $this;
    }

    public function getEmailEntreprise(): ?string
    {
        return $this->emailEntreprise;
    }

    public function setEmailEntreprise(?string $emailEntreprise): self
    {
        $this->emailEntreprise = $emailEntreprise;

        return $this;
    }

    /**
     * @return Collection|Utilisateur[]
     */
    public function getUtilisateurs(): Collection
    {
        return $this->utilisateurs;
    }

    public function addUtilisateur(Utilisateur $utilisateur): self
    {
        if (!$this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs[] = $utilisateur;
            $utilisateur->setEntreprise($this);
        }

        return $this;
    }

    public function removeUtilisateur(Utilisateur $utilisateur): self
    {
        if ($this->utilisateurs->contains($utilisateur)) {
            $this->utilisateurs->removeElement($utilisateur);
            // set the owning side to null (unless already changed)
            if ($utilisateur->getEntreprise() === $this) {
                $utilisateur->setEntreprise(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Compte[]
     */
    public function getComptes(): Collection
    {
        return $this->comptes;
    }

    public function addCompte(Compte $compte): self
    {
        if (!$this->comptes->contains($compte)) {
            $this->comptes[] = $compte;
            $compte->setEntreprise($this);
        }

        return $this;
    }

    public function removeCompte(Compte $compte): self
    {
        if ($this->comptes->contains($compte)) {
            $this->comptes->removeElement($compte);
            // set the owning side to null (unless already changed)
            if ($compte->getEntreprise() === $this) {
                $compte->setEntreprise(null);
            }
        }

        return $this;
    }
}
