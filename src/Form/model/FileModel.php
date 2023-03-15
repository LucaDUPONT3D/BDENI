<?php

namespace App\Form\model;

class FileModel
{
    private $fichier;

    /**
     * @return mixed
     */
    public function getFichier()
    {
        return $this->fichier;
    }

    /**
     * @param mixed $fichier
     */
    public function setFichier($fichier): void
    {
        $this->fichier = $fichier;
    }


}