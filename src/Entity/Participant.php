<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ParticipantRepository")
 */
class Participant implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="1", max="255", minMessage="Nom trop court. {{ limit }} caractères minimum!", maxMessage="Nom trop long.{{ limit }} caractères maximum!")
     * @Assert\NotBlank(message="Le nom est obligatoire!")
     */
    private $nom;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="1", max="255", minMessage="Prenom trop court. {{ limit }} caractères minimum!", maxMessage="Prenom trop long.{{ limit }} caractères maximum!")
     * @Assert\NotBlank(message="Le prenom est obligatoire!")
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=20)
     * @Assert\Length(min="10", max="20", minMessage="Numéro trop court. {{ limit }} chiffres minimum!", maxMessage="Numéro trop long.{{ limit }} chiffres maximum!")
     * @Assert\NotBlank(message="Le numéro est obligatoire!")
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email(message = "L'email '{{ value }}' n'est pas un email valide.")
     * @Assert\NotBlank(message="L'email est obligatoire!")
     */
    private $mail;


    /**
     * @ORM\Column(type="boolean")
     */
    private $administrateur;

    /**
     * @ORM\Column(type="boolean")
     */
    private $actif;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="organisateur", cascade={"remove"})
     */
    private $sorties;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="participant")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Length(min="1", max="255", minMessage="Pseudo trop court. {{ limit }} caractères minimum!", maxMessage="Pseudo trop long.{{ limit }} caractères maximum!")
     * @Assert\NotBlank(message="Le pseudo est obligatoire!")
     */
    private $pseudo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(min="6", max="255", minMessage="Mot de passe trop court. {{ limit }} caractères minimum!", maxMessage="Mot de passe trop long.{{ limit }} caractères maximum!")
     * @Assert\NotBlank(message="Le mot de passe est obligatoire!")
     */
    private $motDePasse;

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getMail(): ?string
    {
        return $this->mail;
    }

    public function setMail(string $mail): self
    {
        $this->mail = $mail;

        return $this;
    }

    public function getAdministrateur(): ?bool
    {
        return $this->administrateur;
    }

    public function setAdministrateur(bool $administrateur): self
    {
        $this->administrateur = $administrateur;

        return $this;
    }

    public function getActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    public function addSorty(Sortie $sorty): self
    {
        if (!$this->sorties->contains($sorty)) {
            $this->sorties[] = $sorty;
            $sorty->setOrganisateur($this);
        }

        return $this;
    }

    public function removeSorty(Sortie $sorty): self
    {
        if ($this->sorties->contains($sorty)) {
            $this->sorties->removeElement($sorty);
            // set the owning side to null (unless already changed)
            if ($sorty->getOrganisateur() === $this) {
                $sorty->setOrganisateur(null);
            }
        }

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getPseudo(): ?string
    {
        return $this->pseudo;
    }

    public function setPseudo(string $pseudo): self
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function setPhoto( $photo): self
    {
        $this->photo = $photo;

        return $this;
    }

    public function getMotDePasse(): ?string
    {
        return $this->motDePasse;
    }

    public function setMotDePasse(string $motDePasse): self
    {
        $this->motDePasse = $motDePasse;

        return $this;
    }

	/**
	 * @inheritDoc
	 */
	public function getRoles()
	{
       if( $this->getAdministrateur() == 1){
           return ['ROLE_ADMIN'];
       }
       return ['ROLE_USER'];
	}

	/**
	 * @inheritDoc
	 */
	public function getPassword()
	{
		return $this->motDePasse;
	}

	/**
	 * @inheritDoc
	 */
	public function getSalt()
	{
		return null;
	}

	/**
	 * @inheritDoc
	 */
	public function getUsername()
	{
		return $this->pseudo;
	}

	/**
	 * @inheritDoc
	 */
	public function eraseCredentials()
	{

	}
}
