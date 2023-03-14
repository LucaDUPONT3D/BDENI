<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\FiltreType;
use App\Form\model\Model;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/admin', name: 'admin_')]
class AdminController extends AbstractController
{



//    #[Route('/loadCSV', name: 'load_csv')]
//    public function loadCSV(): Response
//    {
//
//        return $this->render('');
//    }
//    #[Route('/{id}/delete', name: 'delete')]
//    public function delete(): Response
//    {
//
//        return $this->render('');
//    }
}
