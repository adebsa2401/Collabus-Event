<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    #[Route('/dashboard', name: 'app_home')]
    public function home(Security $security): Response
    {
        if ($security->isGranted('ROLE_ADMIN')) {
            return $this->render('admin_dashboard.html.twig');
        }

        if ($security->isGranted('ROLE_INDIVIDUAL')) {
            return $this->render('individual_dashboard.html.twig');
        }

        if ($security->isGranted('ROLE_COMPANY')) {
            return $this->render('company_dashboard.html.twig');
        }
        
        return $this->redirectToRoute('app_login');
    }
}
