<?php

namespace App\Form\model;

use Symfony\Component\Validator\Constraints as Assert;

class Model
{
    private $campus;
    private $recherche;

    #[Assert\LessThan(propertyPath: 'et', message: 'La date de début doit être inférieur à la date de fin')]
    private ?\DateTimeInterface $entre = null;

    #[Assert\GreaterThan(propertyPath: 'entre', message: "La date fin doit être supérieur à la date de début")]

    private  ?\DateTimeInterface $et = null;

    private $organisateur;
    private $inscrit;
    private $pasInscrit;

    private $passe;

    /**
     * @return mixed
     */
    public function getCampus()
    {
        return $this->campus;
    }

    /**
     * @param mixed $campus
     */
    public function setCampus($campus): void
    {
        $this->campus = $campus;
    }

    /**
     * @return mixed
     */
    public function getRecherche()
    {
        return $this->recherche;
    }

    /**
     * @param mixed $recherche
     */
    public function setRecherche($recherche): void
    {
        $this->recherche = $recherche;
    }

    /**
     * @return mixed
     */
    public function getEntre()
    {
        return $this->entre;
    }

    /**
     * @param mixed $entre
     */
    public function setEntre($entre): void
    {
        $this->entre = $entre;
    }

    /**
     * @return mixed
     */
    public function getEt()
    {
        return $this->et;
    }

    /**
     * @param mixed $et
     */
    public function setEt($et): void
    {
        $this->et = $et;
    }

    /**
     * @return mixed
     */
    public function getOrganisateur()
    {
        return $this->organisateur;
    }

    /**
     * @param mixed $organisateur
     */
    public function setOrganisateur($organisateur): void
    {
        $this->organisateur = $organisateur;
    }

    /**
     * @return mixed
     */
    public function getInscrit()
    {
        return $this->inscrit;
    }

    /**
     * @param mixed $inscrit
     */
    public function setInscrit($inscrit): void
    {
        $this->inscrit = $inscrit;
    }

    /**
     * @return mixed
     */
    public function getPasInscrit()
    {
        return $this->pasInscrit;
    }

    /**
     * @param mixed $pasInscrit
     */
    public function setPasInscrit($pasInscrit): void
    {
        $this->pasInscrit = $pasInscrit;
    }

    /**
     * @return mixed
     */
    public function getPasse()
    {
        return $this->passe;
    }

    /**
     * @param mixed $passe
     */
    public function setPasse($passe): void
    {
        $this->passe = $passe;
    }

}

