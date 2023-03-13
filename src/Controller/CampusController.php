<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Ville;
use App\Form\CampusType;
use App\Form\model\RechercheVilleModel;
use App\Form\RechercheType;
use App\Repository\CampusRepository;

use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin', name: 'admin_')]
class CampusController extends AbstractController
{
    #[Route('/add_campus', name: 'add_campus')]
    public function add_campus(CampusRepository $campusRepository, Request $request): Response
    {
        $listeCampus = $campusRepository->findAll();
        $campusRecherche = new RechercheVilleModel();
        $formRecherche = $this->createForm(RechercheType::class, $campusRecherche);
        $formRecherche->handleRequest($request);
        if ($formRecherche->isSubmitted() && $formRecherche->isValid()) {
            $listeCampus = $campusRepository->findAllSearch($campusRecherche);
        }
        $campus = new Campus();
        $campusForm = $this->createForm(CampusType::class, $campus);
        $campusForm->handleRequest($request);
        if ($campusForm->isSubmitted() && $campusForm->isValid()) {
            $campusRepository->save($campus, true);
            $this->addFlash('réussi', 'Ajout reussi');

        }

        return $this->render('admin/campus/add_campus.html.twig', [
            'listeCampus' => $listeCampus,
            'formRecherche' => $formRecherche->createView(),
            'campusForm' => $campusForm->createView()

        ]);
    }

    #[Route('/delete_campus/{id}', name: 'delete_campus', requirements: ["page" => "\d+"])]
    public function delete_campus(CampusRepository $campusRepository, Request $request, int $id, SortieRepository $sortieRepository): Response
    {
        $campusASuprimer = new Campus();
        $campusASuprimer = $campusRepository->find($id);
        $reussi = $sortieRepository->findBy(['campus' => $campusASuprimer]);

        if ($reussi) {
            $this->addFlash('echec', 'Supression imposible campus utiliser dans une autre page');
        } else {
            $campusRepository->remove($campusASuprimer, true);
            $this->addFlash('reussi', 'Supression réussi');
        }
        return $this->redirectToRoute('admin_add_campus');


    }

    #[Route('/update_campus/{id}', name: 'update_campus', requirements: ["page" => "\d+"])]
    public function update_campus(CampusRepository $campusRepository, Request $request, int $id): Response
    {

        $updateCampus = new  Campus();
        $updateCampus = $campusRepository->find($id);
        $campusform = $this->createForm(CampusType::class, $updateCampus );
        $campusform->handleRequest($request);

        if ($campusform->isSubmitted() && $campusform->isValid()){
            $campusRepository->save($updateCampus, true);
            $this->addFlash('reussi', 'modification reussie');
          $returner =  $this->redirectToRoute('admin_add_campus');
        }else{
            $returner =  $this->render('admin/update_campus.html.twig', [
                'campusform' => $campusform->createView()
            ]);
        }

        return $returner;
    }
}

