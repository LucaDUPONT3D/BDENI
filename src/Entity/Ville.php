<?php

namespace App\Entity;

use App\Repository\VilleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: VilleRepository::class)]
class Ville
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

    #[ORM\Column(length: 5)]
    #[Assert\NotBlank(message: 'Le code postal est obligatoire')]
    #[Assert\Length(max: 5, maxMessage: 'Le code postal ne doit pas faire plus de {{ limit }} caractères')]
    #[Assert\Regex('/(0[1-9]\d{3})$|^([1-9]\d{4})/',message: 'Le code postale n\'est pas au bon format')]
    #[Groups(['api_lieu_show_one', 'api_lieu_show_ville', 'api_sortie_show_all', 'api_sortie_show_one'])]
    private ?string $codePostal = null;

    #[ORM\OneToMany(mappedBy: 'ville', targetEntity: Lieu::class, cascade: ["remove"])]
    private Collection $lieu;

    public function __construct()
    {
        $this->lieu = new ArrayCollection();
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

    public function getCodePostal(): ?string
    {
        return $this->codePostal;
    }

    public function setCodePostal(string $codePostal): self
    {
        $this->codePostal = $codePostal;

        return $this;
    }

    /**
     * @return Collection<int, Lieu>
     */
    public function getLieu(): Collection
    {
        return $this->lieu;
    }

    public function addLieu(Lieu $lieu): self
    {
        if (!$this->lieu->contains($lieu)) {
            $this->lieu->add($lieu);
            $lieu->setVille($this);
        }

        return $this;
    }

    public function removeLieu(Lieu $lieu): self
    {
        if ($this->lieu->removeElement($lieu)) {
            // set the owning side to null (unless already changed)
            if ($lieu->getVille() === $this) {
                $lieu->setVille(null);
            }
        }

        return $this;
    }
}
