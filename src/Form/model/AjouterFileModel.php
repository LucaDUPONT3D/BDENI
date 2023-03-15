<?php

namespace App\Form\model;

class AjouterFileModel
{
    private $ajouter;
    /**
 * @return mixed
 */
public function getAjouter()
{
    return $this->ajouter;
}/**
 * @param mixed $ajouter
 */
public function setAjouter($ajouter): void
{
    $this->ajouter = $ajouter;
}


}