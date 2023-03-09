<?php

namespace App\Utils;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use DateInterval;

class EtatSortieManager
{

    public function __construct(private EtatRepository $etatRepository, private SortieRepository $sortieRepository)
    {
    }

    public function checkEtatSortie(array $sorties): array
    {
        foreach ($sorties as $sortie) {
            if ($sortie instanceof Sortie && $sortie->getEtat()->getLibelle() != 'Créée') {

                if ($sortie->getEtat()->getLibelle() == 'Ouverte') {
                    if ($sortie->getParticipants()->count() >= $sortie->getNbInsriptionsMax() ||
                        $sortie->getDateLimiteInscription() < new \DateTime('now')) {
                        $this->setEtat($sortie, 3);
                    } elseif ($sortie->getDateHeureDebut() >= new \DateTime('now')) {
                        $this->setEtat($sortie, 4);
                    } elseif ((date_add($sortie->getDateHeureDebut(),
                            DateInterval::createFromDateString($sortie->getDuree() . 'minute')))
                        <= new \DateTime('now')) {
                        $this->setEtat($sortie, 5);
                    } elseif ((date_add($sortie->getDateHeureDebut(),
                            DateInterval::createFromDateString($sortie->getDuree() + 43200 . 'minute')))
                        >= new \DateTime('now')) {
                        $this->setEtat($sortie, 7);
                    } else {
                        $this->setEtat($sortie, 2);
                    }
                } elseif ($sortie->getEtat()->getLibelle() == 'Activité en cours') {
                    if ((date_add($sortie->getDateHeureDebut(),
                            DateInterval::createFromDateString($sortie->getDuree() . 'minute')))
                        <= new \DateTime('now')) {
                        $this->setEtat($sortie, 5);
                    } elseif ((date_add($sortie->getDateHeureDebut(),
                            DateInterval::createFromDateString($sortie->getDuree() + 43200 . 'minute')))
                        >= new \DateTime('now')) {
                        $this->setEtat($sortie, 7);
                    }
                } elseif ($sortie->getEtat()->getLibelle() == 'Passée') {
                    if ((date_add($sortie->getDateHeureDebut(),
                            DateInterval::createFromDateString($sortie->getDuree() + 43200 . 'minute')))
                        >= new \DateTime('now')) {
                        $this->setEtat($sortie, 7);
                    }
                } elseif ($sortie->getEtat()->getLibelle() == 'Annulée') {
                    if ((date_add($sortie->getDateHeureDebut(),
                            DateInterval::createFromDateString($sortie->getDuree() + 43200 . 'minute')))
                        >= new \DateTime('now')) {
                        $this->setEtat($sortie, 7);
                    }
                }
            }
        }

        return $sorties;
    }

    private function setEtat(Sortie $sortie, int $etatId)
    {
        $sortie->setEtat($this->etatRepository->find($etatId));
        $this->sortieRepository->save($sortie, true);
    }

}
