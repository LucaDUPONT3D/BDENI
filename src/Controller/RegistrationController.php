<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Ville;
use App\Form\model\RechercheVilleModel;
use App\Form\RechercheType;
use App\Form\RegistrationFormType;
use App\Form\VilleType;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/admin', name: 'admin_')]
class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setRoles(['ROLE_USER']);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash("primary", "Utilisateur inscrit !");

            return $this->redirectToRoute('main_home');

        }

        return $this->render('admin/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/addville', name: 'ville')]
    public function addville(Request $request, VilleRepository $villeRepository): Response
    {
        $tableauVille = $villeRepository->findAll();
        $recherche = new RechercheVilleModel();
        $villeform = $this->createForm(RechercheType::class, $recherche);
        $villeform->handleRequest($request);


        if ($villeform->isSubmitted() && $villeform->isValid()) {

            $tableauVille = $villeRepository->findAllSearch($recherche);

        }

        $ville = new Ville();
        $addVille = $this->createForm(VilleType::class, $ville);
        $addVille->handleRequest($request);
        if ($addVille->isSubmitted() && $addVille->isValid()) {

            $villeRepository->save($ville, true);
            return $this->redirectToRoute('admin_ville');
        }


        return $this->render('admin/addville.html.twig', [
            'tableauVille' => $tableauVille,
            'villeForm' => $villeform->createView(),
            'addville' => $addVille->createView()


        ]);

    }

    #[Route('/delete_ville{id}', name: 'delete_ville', requirements: ["id" => "\d+"])]
    public function delete_ville(VilleRepository $villeRepository, int $id, LieuRepository $lieuRepository): Response
    {

        $villeasuprimer = new Ville();
        $villeasuprimer = $villeRepository->find($id);
       $reussi= $lieuRepository->findBy(['ville' => $villeasuprimer]);

        if ($reussi) {
            $this->addFlash('echec', 'Supression imposible ville utiliser dans une autre page');
        } else {
            $villeRepository->remove($villeasuprimer, true);
            $this->addFlash('reussi', 'Supression réussi');
        }


        return $this->redirectToRoute('admin_ville');
    }
    #[Route('/update_ville{id}', name: 'update_ville', requirements: ["id" => "\d+"])]
    public function update_ville(Request $request, VilleRepository $villeRepository, int $id): Response
    {

        $ville = new Ville();
        $ville = $villeRepository->find($id);
        $updateVille = $this->createForm(VilleType::class, $ville);
        $updateVille->handleRequest($request);
        if ($updateVille->isSubmitted() && $updateVille->isValid()) {
            $villeRepository->save($ville, true);
            $this->addFlash('reussi', 'Modification Réussie');
           return  $this->redirectToRoute('admin_ville');
        }


        return $this->render('admin/update_ville.html.twig', [
            'updateVille' => $updateVille->createView()
        ]);

    }

}
