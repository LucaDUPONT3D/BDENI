<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\model\FileModel;
use App\Form\model\ModelCampusVille;
use App\Form\FiltreCampusVille;
use App\Form\RegistationFormCSVType;
use App\Form\RegistrationFormType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\LieuRepository;
use App\Repository\UserRepository;
use App\Repository\VilleRepository;
use App\Security\UserAuthenticator;
use App\Utils\Uploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

#[Route('/admin', name: 'admin_')]
class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'register')]
    public function register(UserRepository $userRepository, CampusRepository $campusRepository, Uploader $uploader, Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
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


        $ajoutfileModel = new FileModel();
        $csvForm = $this->createForm(RegistationFormCSVType::class, $ajoutfileModel);
        $csvForm->handleRequest($request);
        if ($csvForm->isSubmitted() && $csvForm->isValid()) {

            /**
             * @var UploadedFile $file
             */
            $file = $csvForm->get('fichier')->getData();

            if ($file) {
                $x = 0;
                if (($handle = fopen($file, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                        $x++;
                        if ($x > 1)             // pour sauter la 1 lignes d'ent??te
                        {
                            $user = new User();
                            $campus = new Campus();
                            $campus =$campusRepository->find($data[6]);

                            $user->setEmail($data[0]);
                            $user->setPseudo($data[1]);
                            $user->setNom($data[2]);
                            $user->setPrenom($data[3]);
                            $user->setTelephone($data[4]);
                            $user->setPassword($data[5]);
                            $user->setCampus($campus) ;
                            $user->setRoles(['ROLE_USER']);
                            $user->setPassword(
                                $userPasswordHasher->hashPassword(

                                    $user,
                                    $data[5]
                                )
                            );
                            $entityManager->persist($user);
                        }

                    }
                $entityManager->flush();

                }
            }
        }


            return $this->render('admin/register.html.twig', [
                'registrationForm' => $form->createView(),
                'csvForm' =>$csvForm->createView()
            ]);
        }

    }
