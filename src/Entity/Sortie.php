<?php

namespace App\Entity;

use App\Repository\SortieRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: SortieRepository::class)]
#[ORM\HasLifecycleCallbacks]

class Sortie
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "Le nom est obligatoire")]
    #[Assert\Length(max: 255 , maxMessage: "Le nom ne doit pas faire plus de {{ limit }} caractères")]
    #[Assert\Regex('/\w+/', message: 'Le nom ne doit contenir que des lettres, chiffres ou underscore')]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?string $nom = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'La date d\'entrée est obligatoire')]
    #[Assert\GreaterThan('today UTC', message: "La date d'entrée doit être supérieur à la date d'aujourd'hui")]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?\DateTimeInterface $dateHeureDebut = null;

    #[ORM\Column]
    #[Assert\NotBlank(message: "La durée est obligatoire")]
    #[Assert\Regex('/\d+/', message: 'La durée doit être un nombre')]
    #[Assert\Positive(message: 'La durée doit être positive')]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?int $duree = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank(message: 'La date d\'inscription est obligatoire')]
    #[Assert\LessThan(propertyPath: 'dateHeureDebut',
        message: 'La date d\'inscription doit être inférieur à la date de sortie')]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?\DateTimeInterface $dateLimiteInscription = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: "Le nombre d'inscription est obligatoire")]
    #[Assert\Regex('/\d+/', message: 'Le nombre d\'inscription doit être un nombre')]
    #[Assert\Positive(message: 'Le nombre d\'inscription doit être positif')]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?int $nbInsriptionsMax = null;


    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: "La déscription est obligatoire")]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?string $infosSortie = null;

    #[ORM\ManyToOne(inversedBy: 'sortie')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?Etat $etat = null;

    #[ORM\ManyToOne(inversedBy: 'sortie')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?Lieu $lieu = null;

    #[ORM\ManyToOne(inversedBy: 'sortie')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?Campus $campus = null;

    #[ORM\ManyToOne(inversedBy: 'sorties')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?User $organisateur = null;

    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'inscriptions')]
    private Collection $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
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

    public function getDateHeureDebut(): ?\DateTimeInterface
    {
        return $this->dateHeureDebut;
    }

    public function setDateHeureDebut(?\DateTimeInterface $dateHeureDebut): self
    {
        $this->dateHeureDebut = $dateHeureDebut;

        return $this;
    }

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateLimiteInscription(): ?\DateTimeInterface
    {
        return $this->dateLimiteInscription;
    }

    public function setDateLimiteInscription(?\DateTimeInterface $dateLimiteInscription): self
    {
        $this->dateLimiteInscription = $dateLimiteInscription;

        return $this;
    }

    public function getNbInsriptionsMax(): ?int
    {
        return $this->nbInsriptionsMax;
    }

    public function setNbInsriptionsMax(?int $nbInsriptionsMax): self
    {
        $this->nbInsriptionsMax = $nbInsriptionsMax;

        return $this;
    }

    public function getInfosSortie(): ?string
    {
        return $this->infosSortie;
    }

    public function setInfosSortie(string $infosSortie): self
    {
        $this->infosSortie = $infosSortie;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

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

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants->add($participant);
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        $this->participants->removeElement($participant);


        return $this;
    }


}
