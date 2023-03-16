<?php

namespace App\Entity;

use App\Repository\EtatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EtatRepository::class)]
class Etat
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?int $id= null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Le libelle est obligatoire')]
    #[Assert\Length(max: 255, maxMessage: 'Le libelle  ne doit pas faire plus de {{ limit }} caractères')]
    #[Groups(['api_sortie_show_all', 'api_sortie_show_one'])]
    private ?string $libelle = null;

    #[ORM\OneToMany(mappedBy: 'etat', targetEntity: Sortie::class, cascade: ["remove"])]
    private Collection $sortie;

    public function __construct()
    {
        $this->sortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLibelle(): ?string
    {
        return $this->libelle;
    }

    public function setLibelle(string $libelle): self
    {
        $this->libelle = $libelle;

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
            $sortie->setEtat($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): self
    {
        if ($this->sortie->removeElement($sortie)) {
            // set the owning side to null (unless already changed)
            if ($sortie->getEtat() === $this) {
                $sortie->setEtat(null);
            }
        }

        return $this;
    }
}
