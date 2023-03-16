<?php

namespace App\Entity;

use App\Repository\LieuRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: LieuRepository::class)]
class Lieu
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api_lieu_show_one', 'api_lieu_show_ville', 'api_sortie_show_all', 'api_sortie_show_one'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le nom est obligatoire')]
    #[Assert\Length(max: 255, maxMessage: 'Le nom ne doit pas faire plus de {{ limit }} caractères')]
    #[Groups(['api_lieu_show_one', 'api_lieu_show_ville', 'api_sortie_show_all', 'api_sortie_show_one'])]
    private ?string $nom = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'La rue est obligatoire')]
    #[Assert\Length(max: 255, maxMessage: 'La rue ne doit pas faire plus de {{ limit }} caractères')]
    #[Groups(['api_lieu_show_one', 'api_lieu_show_ville', 'api_sortie_show_all', 'api_sortie_show_one'])]
    private ?string $rue = null;

    #[ORM\Column]
    #[Groups(['api_lieu_show_one', 'api_lieu_show_ville', 'api_sortie_show_all', 'api_sortie_show_one'])]
    #[Assert\NotBlank(message: 'La lattitude est obligatoire')]
    #[Assert\Type(type: 'numeric')]
    private ?float $latitude = null;

    #[ORM\Column]
    #[Groups(['api_lieu_show_one', 'api_lieu_show_ville', 'api_sortie_show_all', 'api_sortie_show_one'])]
    #[Assert\NotBlank(message: 'La longitude est obligatoire')]
    #[Assert\Type(type: 'numeric')]
    private ?float $longitude = null;

    #[ORM\ManyToOne(inversedBy: 'lieu')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['api_lieu_show_one', 'api_lieu_show_ville', 'api_sortie_show_all', 'api_sortie_show_one'])]
    private ?Ville $ville = null;

    #[ORM\OneToMany(mappedBy: 'lieu', targetEntity: Sortie::class, cascade: ["remove"])]
    private Collection $sortie;

    public function __construct()
    {
        $this->sortie = new ArrayCollection();
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

    public function getRue(): ?string
    {
        return $this->rue;
    }

    public function setRue(string $rue): self
    {
        $this->rue = $rue;

        return $this;
    }

    public function getLatitude(): ?float
    {
        return $this->latitude;
    }

    public function setLatitude(float $latitude): self
    {
        $this->latitude = $latitude;

        return $this;
    }

    public function getLongitude(): ?float
    {
        return $this->longitude;
    }

    public function setLongitude(float $longitude): self
    {
        $this->longitude = $longitude;

        return $this;
    }

    public function getVille(): ?Ville
    {
        return $this->ville;
    }

    public function setVille(?Ville $ville): self
    {
        $this->ville = $ville;

        return $this;
    }

    /**
     * @return Collection<int, Sortie>
     */
    public function getSortie(): Collection
    {
        return $this->sortie;
    }

    public function addSortie(Sortie $sortie): self
    {
        if (!$this->sortie->contains($sortie)) {
            $this->sortie->add($sortie);
            $sortie->setLieu($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): self
    {
        if ($this->sortie->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getLieu() === $this) {
                $sortie->setLieu(null);
            }
        }

        return $this;
    }
}
