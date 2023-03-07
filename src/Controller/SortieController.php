<?php

namespace App\Controller;

use App\Form\FiltreType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'all')]
    public function afficher(SortieRepository $sortieRepository, Request $request): Response
    {
        $formFiltre = $this->createForm(FiltreType::class);
        $sortie = $sortieRepository->findALLjoin();
        $formFiltre->handleRequest($request);
        if ($formFiltre->isSubmitted() && $formFiltre->isValid()){
       // $sortieRepository->findBy($sortieRepository->findALLjoin(),[$formFiltre->get('Campus')]);

        }


        return $this->render('sortie/afficher.html.twig', [
            'sorties' => $sortie,
            'form' => $formFiltre->createView()
        ]);
    }
}
