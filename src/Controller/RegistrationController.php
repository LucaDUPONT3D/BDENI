<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Ville;
use App\Form\model\RechercheVilleModel;
use App\Form\RechercheType;
use App\Form\RegistrationFormType;
use App\Form\VilleType;
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

            $entityManager->persist($user);
            $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
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
        }


        return $this->render('admin/addville.html.twig', [
            'tableauVille' => $tableauVille,
            'villeForm' => $villeform->createView(),
            'addville'=>$addVille->createView()


        ]);

    }


}
