<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\FiltreRechercheRepository")
 */
class FiltreRecherche
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateDebutRecherche;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateFinRecherche;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $checkOrganisateur;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $checkUserInscrit;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $checkUserPasInscrit;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $checkSortiesPassees;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site")
     */
    private $siteId;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $recherche;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDebutRecherche(): ?\DateTimeInterface
    {
        return $this->dateDebutRecherche;
    }

    public function setDateDebutRecherche(?\DateTimeInterface $dateDebutRecherche): self
    {
        $this->dateDebutRecherche = $dateDebutRecherche;

        return $this;
    }

    public function getDateFinRecherche(): ?\DateTimeInterface
    {
        return $this->dateFinRecherche;
    }

    public function setDateFinRecherche(?\DateTimeInterface $dateFinRecherche): self
    {
        $this->dateFinRecherche = $dateFinRecherche;

        return $this;
    }

    public function getCheckOrganisateur(): ?bool
    {
        return $this->checkOrganisateur;
    }

    public function setCheckOrganisateur(bool $checkOrganisateur): self
    {
        $this->checkOrganisateur = $checkOrganisateur;

        return $this;
    }

    public function getCheckUserInscrit(): ?bool
    {
        return $this->checkUserInscrit;
    }

    public function setCheckUserInscrit(bool $checkUserInscrit): self
    {
        $this->checkUserInscrit = $checkUserInscrit;

        return $this;
    }

    public function getCheckUserPasInscrit(): ?bool
    {
        return $this->checkUserPasInscrit;
    }

    public function setCheckUserPasInscrit(bool $checkUserPasInscrit): self
    {
        $this->checkUserPasInscrit = $checkUserPasInscrit;

        return $this;
    }

    public function getCheckSortiesPassees(): ?bool
    {
        return $this->checkSortiesPassees;
    }

    public function setCheckSortiesPassees(bool $checkSortiesPassees): self
    {
        $this->checkSortiesPassees = $checkSortiesPassees;

        return $this;
    }

    public function getSiteId(): ?Site
    {
        return $this->siteId;
    }

    public function setSiteId(?Site $siteId): self
    {
        $this->siteId = $siteId;

        return $this;
    }

    public function getRecherche(): ?string
    {
        return $this->recherche;
    }

    public function setRecherche(?string $recherche): self
    {
        $this->recherche = $recherche;

        return $this;
    }
}
