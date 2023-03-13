<?php

namespace App\Controller;


use App\Entity\Lieu;
use App\Form\LieuType;
use App\Repository\LieuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/lieu', name: 'lieu_')]
class LieuController extends AbstractController
{

    #[Route('/add', name: 'add')]
    public function add(Request $request, LieuRepository $lieuRepository): Response
    {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);

        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $lieuRepository->save($lieu, true);

            $this->addFlash("success", "Lieu créé !");

            return $this->redirectToRoute('sortie_add');
        }

        return $this->render('lieu/add.html.twig', ['lieuForm'=> $lieuForm->createView()]);
    }
}
