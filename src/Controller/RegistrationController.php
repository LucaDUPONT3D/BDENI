<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\User;
use App\Entity\Ville;
use App\Form\AjouterFileType;
use App\Form\model\AjouterFileModel;
use App\Form\model\ModelCampusVille;
use App\Form\FiltreCampusVille;
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
class RegistrationController extends AbstractController
{

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();

            // do anything else you need here, like send an email

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
////upload photo
///**
// * @var UploadedFile $file
// */
//$file = $form->get('fichier')->getData();
//if ($file) {
//    $x = 0;
//    if (($handle = fopen($file, "r")) !== FALSE) {
//        while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
//            $x++;
//            if ($x > 1)             // pour sauter la 1 lignes d'entête
//            {
//                $user = new User();
//                $campus = new Campus();
//                $campus->setNom($data[6]);
//                $campusRepository->save($campus, true);
//                $user->setEmail($data[0]);
//                $user->setPseudo($data[1]);
//                $user->setNom($data[2]);
//                $user->setPrenom($data[3]);
//                $user->setTelephone($data[4]);
//                $user->setPassword($data[5]);
//                $user->setCampus($campus);
//                $user->setRoles(['ROLE_USER']);
//                $userRepository->save($user, true);
//                $user->setPassword(
//                    $userPasswordHasher->hashPassword(
//                        $user,
//                        $form->get('plainPassword')->getData()
//                    )
//                );
//
//            }
//        }
//
//    }
//
//}
