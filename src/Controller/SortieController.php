<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\FiltreType;
use App\Entity\Sortie;
use App\Form\model\Model;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'all')]
    public function afficher(SortieRepository $sortieRepository, Request $request, Model $model): Response
    {
        $formFiltre = $this->createForm(FiltreType::class);

        $formFiltre->handleRequest($request);

        if ($formFiltre->isSubmitted() && $formFiltre->isValid()) {

//            $campus = $formFiltre->get('campus')->getData();
//
//
//            $recherche = $formFiltre->get('recherche')->getData();
//            $entre = $formFiltre->get('entre')->getData();
//            $et = $formFiltre->get('et')->getData();







            $sorties = $sortieRepository->findALLFilter( $model);

        } else {

            $sorties = $sortieRepository->findALLjoin();
        }
        return $this->render('sortie/afficher.html.twig', [
            'sorties' => $sorties,
            'form' => $formFiltre->createView()
        ]);
    }

    #[Route('/search', name: 'search')]
    public function search(SortieRepository $sortieRepository, Request $request): Response
    {

        return $this->render('sortie/afficher.html.twig');
    }

    #[Route('/{id}', name: 'show_one')]
    public function show(SortieRepository $sortieRepository, Request $request, Sortie $id): Response
    {

        return $this->render('sortie/show.html.twig', ['sortie' => $id]);
    }

    #[Route('/add', name: 'add')]
    public function add(SortieRepository $sortieRepository, Request $request): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        return $this->render('sortie/add.html.twig', ['sortieForm' => $sortieForm->createView()]);
    }

    #[Route('/update/{id}', name: 'update', requirements: ['id' => '\d+'])]
    public function update(SortieRepository $sortieRepository, Request $request, int $id): Response
    {


        return $this->render('sortie/update.html.twig', ['sortie' => $id]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements: ['id' => '\d+'])]
    public function delete(SortieRepository $sortieRepository, Request $request, Sortie $id): Response
    {

        return $this->render('sortie/afficher.html.twig');
    }

    #[Route('/cancel/{id}', name: 'cancel', requirements: ['id' => '\d+'])]
    public function cancel(SortieRepository $sortieRepository, Request $request, Sortie $id): Response
    {

        return $this->render('sortie/cancel.html.twig', ['sortie' => $id]);
    }

    #[Route('/publish/{id}', name: 'publish', requirements: ['id' => '\d+'])]
    public function publish(SortieRepository $sortieRepository, EtatRepository $etatRepository, Sortie $id): Response
    {

        $id->setEtat($etatRepository->find(2));

        $sortieRepository->save($id, true);

        return $this->render('sortie/show.html.twig', ['sortie' => $id]);
    }

    #[Route('/subscribe/{id}', name: 'subscribe', requirements: ['id' => '\d+'])]
    public function subscribe(SortieRepository $sortieRepository, Request $request, int $id): Response
    {

        $sortie = $sortieRepository->find($id);


    ;
        if (($sortie->getDateLimiteInscription() > date('now')&& $sortie->getEtat()->getLibelle()=='Ouverte' && $sortie->getNbInsriptionsMax()- count($sortie->getParticipants())>0 ) ){
            $user = $this->getUser();

            $sortie->addParticipant($user);
            $sortieRepository->save($sortie, true);


            $resultat = $this->render('sortie/show.html.twig', ['sortie' => $sortie]);

        }
        else{

            $resultat = $this->redirectToRoute('sortie_all');
        }
        return $resultat;
    }

    #[Route('/unsubscride/{id}', name: 'unsubscride', requirements: ['id' => '\d+'])]
    public function unsubscride(SortieRepository $sortieRepository, Request $request, int $id): Response
    {
        $sortie = $sortieRepository->find($id);
    if ($sortie->getEtat()->getLibelle()!='Activité en cours'){
        $sortie->removeParticipant($this->getUser());

        $sortieRepository->save($sortie, true);
        $resultat = $this->render('sortie/show.html.twig', ['sortie' => $sortie]);
    }else{
        $resultat = $this->redirectToRoute('sortie_all');
    }


        return $resultat;
    }


}
