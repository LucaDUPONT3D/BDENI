<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['pseudo'], message: 'Un compte avec ce pseudo existe déja')]
#[UniqueEntity(fields: ['email'], message: 'Un compte avec cet email existe déja')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "L'email est obligatoire")]
    #[Assert\Email(message: 'Le champ attend un email')]
    #[Assert\Length(max: 180 , maxMessage: "Le mail ne doit pas faire plus de {{ limit }} caractères")]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\Column(type: 'boolean')]
    private $isVerified = false;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length( max: 255 , maxMessage: "Le nom ne doit pas faire plus de {{ limit }} caractères")]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le prénom est obligatoire")]
    #[Assert\Length( max: 255 , maxMessage: "Le prénom ne doit pas faire plus de {{ limit }} caractères")]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?string $prenom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le téléphone est obligatoire")]
    #[Assert\Regex('/0[1-79]([-. \/]?\d{2}){4}/', message: 'Merci d\'indiquer un numéro de téléphone valide')]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?string $telephone = null;

    #[ORM\Column]
    private ?bool $actif = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $image = null;

    #[ORM\ManyToOne(inversedBy: 'user')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Campus $campus = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le pseudo est obligatoire")]
    #[Assert\Length( max: 255 , maxMessage: "Le pseudo ne doit pas faire plus de {{ limit }} caractères")]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?string $pseudo = null;

    #[ORM\OneToMany(mappedBy: 'organisateur', targetEntity: Sortie::class, cascade: ["remove"])]
    private Collection $sorties;

    #[ORM\ManyToMany(targetEntity: Sortie::class, mappedBy: 'participants', cascade: ["remove"])]
    private Collection $inscriptions;

    /**
     * @return Collection
     */
    public function getSorties(): Collection
    {
        return $this->sorties;
    }

    /**
     * @param Collection $sorties
     */
    public function setSorties(Collection $sorties): void
    {
        $this->sorties = $sorties;
    }

    /**
     * @return Collection
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    /**
     * @param Collection $inscriptions
     */
    public function setInscriptions(Collection $inscriptions): void
    {
        $this->inscriptions = $inscriptions;
    }

    public function __construct()
    {
        $this->sorties = new ArrayCollection();
        $this->inscriptions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function isVerified(): bool
    {
        return $this->isVerified;
    }

    public function setIsVerified(bool $isVerified): self
    {
        $this->isVerified = $isVerified;

        return $this;
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

    public function isActif(): ?bool
    {
        return $this->actif;
    }

    public function setActif(bool $actif): self
    {
        $this->actif = $actif;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

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

    public function getCampus(): ?Campus
    {
        return $this->campus;
    }

    public function setCampus(?Campus $campus): self
    {
        $this->campus = $campus;

        return $this;
    }



    #[ORM\PrePersist]
    public function setCreatedValue(): void
    {
        $this->setActif(true);
    }




}
