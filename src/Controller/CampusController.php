<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Ville;
use App\Form\CampusType;
use App\Form\model\ModelCampusVille;
use App\Form\FiltreCampusVille;
use App\Repository\CampusRepository;

use App\Repository\LieuRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/campus', name: 'admin_campus_')]
class CampusController extends AbstractController
{
    #[Route('/add/{page}', name: 'add')]
    public function add(CampusRepository $campusRepository, Request $request, int $page = 1): Response
    {
        //Créer un formulaire de filtre
        $model = new ModelCampusVille();
        $filtreCampusForm = $this->createForm(FiltreCampusVille::class, $model);
        $filtreCampusForm->handleRequest($request);


        if ($filtreCampusForm->isSubmitted() && $filtreCampusForm->isValid()) {

            $nbSortieMax = count($campusRepository->findAllToCheckFilter($model));
            $maxPage = ceil($nbSortieMax / CampusRepository::CAMPUS_LIMIT);

            $listeCampus = $campusRepository->findAllToDisplayFilter($model, $page);

        } else {
            $nbSortieMax = count($campusRepository->findAllToCheck());
            $maxPage = ceil($nbSortieMax / CampusRepository::CAMPUS_LIMIT);

            $listeCampus = $campusRepository->findAllToDisplay($page);
        }

        //Créer un formulaire de création de campus
        $campus = new Campus();
        $campusForm = $this->createForm(CampusType::class, $campus);
        $campusForm->handleRequest($request);

        if ($campusForm->isSubmitted() && $campusForm->isValid()) {
            $campusRepository->save($campus, true);
            $this->addFlash('primary', 'Campus créé');

            return $this->redirectToRoute('admin_campus_add');

        }

        return $this->render('/admin/campus/add.html.twig', [
            'listeCampus' => $listeCampus,
            'filtreCampusVilleForm' => $filtreCampusForm->createView(),
            'campusForm' => $campusForm->createView(),
            "currentPage" => $page,
            "maxPage" => $maxPage

        ]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ["id" => "\d+"])]
    public function delete(
        CampusRepository $campusRepository,
        int $id,
        SortieRepository $sortieRepository
    ): Response
    {

        $campusASuprimer = $campusRepository->find($id);
        $reussi = $sortieRepository->findBy(['campus' => $campusASuprimer]);

        if ($reussi) {
            $this->addFlash('warning', 'Suppression impossible, campus en cours d\'utilisation');
        } else {
            $campusRepository->remove($campusASuprimer, true);
            $this->addFlash('danger', 'Campus supprimé');
        }
        return $this->redirectToRoute('admin_campus_add');


    }

    #[Route('/update/{id}', name: 'update', requirements: ["id" => "\d+"])]
    public function update(CampusRepository $campusRepository, Request $request, int $id): Response
    {

        $updateCampus = $campusRepository->find($id);
        $campusform = $this->createForm(CampusType::class, $updateCampus);
        $campusform->handleRequest($request);

        if ($campusform->isSubmitted() && $campusform->isValid()) {
            $campusRepository->save($updateCampus, true);
            $this->addFlash('primary', 'Campus modifié');
          $returner =  $this->redirectToRoute('admin_campus_add');
        }else {
            $returner =  $this->render('admin/campus/update.html.twig', [
                'campusform' => $campusform->createView()
            ]);
        }

        return $returner;
    }
}

