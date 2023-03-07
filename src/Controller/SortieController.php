<?php

namespace App\Controller;

use App\Form\FiltreType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'all')]
    public function afficher(SortieRepository $sortieRepository, ): Response
    {
        $formFiltre = $this->createForm(FiltreType::class);
        $sortie = $sortieRepository->findALLjoin();

        return $this->render('sortie/afficher.html.twig', [
            'sorties' => $sortie,
            'form' => $formFiltre->createView()
        ]);
    }
}
