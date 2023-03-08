<?php

namespace App\Controller;

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
    #[Route('/', name: 'show_all')]
    public function showAll(SortieRepository $sortieRepository, Request $request): Response
    {
        $model = new Model();
        $formFiltre = $this->createForm(FiltreType::class, $model);

        $formFiltre->handleRequest($request);

        if ($formFiltre->isSubmitted() && $formFiltre->isValid()) {



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

    #[Route('/{id}', name: 'show_one', requirements:['id' => '\d+'])]
    public function show(Sortie $id): Response
    {
        return $this->render('sortie/show.html.twig', ['sortie'=> $id]);
    }

    #[Route('/add', name: 'add')]
    public function add(SortieRepository $sortieRepository, EtatRepository $etatRepository, Request $request): Response
    {
        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieType::class, $sortie);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $sortie->setOrganisateur($this->getUser());

            if ($request->request->get('submit') == 1) {
                $sortie->setEtat($etatRepository->find(1));
            }elseif ($request->request->get('submit') == 2) {
                $sortie->setEtat($etatRepository->find(2));
            }

            $sortieRepository->save($sortie, true);

            return $this->redirectToRoute('sortie_show_one', ['id' => $sortie->getId()]);
        }

        return $this->render(
            'sortie/add.html.twig',
            ['sortieForm'=> $sortieForm->createView()]
        );
    }

    #[Route('/update/{id}', name: 'update', requirements:['id' => '\d+'])]
    public function update(SortieRepository $sortieRepository, Request $request, Sortie $id): Response
    {
        $sortieForm = $this->createForm(SortieType::class, $id);

        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {

            $sortieRepository->save($id, true);

            return $this->redirectToRoute('sortie_show_one', ['id' => $id->getId()]);
        }

        return $this->render('sortie/update.html.twig', ['sortieForm'=> $sortieForm->createView(),'sortie'=>$id]);
    }

    #[Route('/delete/{id}', name: 'delete', requirements:['id' => '\d+'])]
    public function delete(SortieRepository $sortieRepository, Request $request, Sortie $id): Response
    {
        if ($id) {
            $sortieRepository->remove($id, true);
        } else {
            throw  $this->createNotFoundException("Oops ! Delete not found !");
        }

        return $this->redirectToRoute('main_home');
    }

    #[Route('/cancel/{id}', name: 'cancel', requirements:['id' => '\d+'])]
    public function cancel(SortieRepository $sortieRepository, Request $request, Sortie $id): Response
    {

        return $this->render('sortie/cancel.html.twig', ['sortie'=>$id]);
    }

    #[Route('/publish/{id}', name: 'publish', requirements:['id' => '\d+'])]
    public function publish(SortieRepository $sortieRepository, EtatRepository $etatRepository, Sortie $id): Response
    {

        $id->setEtat($etatRepository->find(2));

        $sortieRepository->save($id, true);

        return $this->render('sortie/show.html.twig', ['sortie'=>$id]);
    }

    #[Route('/subscribe/{id}', name: 'subscribe', requirements:['id' => '\d+'])]
    public function subscribe(SortieRepository $sortieRepository, Sortie $id): Response
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

    #[Route('/unsubscride/{id}', name: 'unsubscride', requirements:['id' => '\d+'])]
    public function unsubscride(SortieRepository $sortieRepository, Sortie $id): Response
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
