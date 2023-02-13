<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(Security $security): Response
    {
        if ($security->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('admin_dashboard');
        }

        if ($security->isGranted('ROLE_INDIVIDUAL')) {
            return $this->redirectToRoute('individual_dashboard');
        }

        if ($security->isGranted('ROLE_COMPANY')) {
            return $this->redirectToRoute('company_dashboard');
        }
        
        return $this->redirectToRoute('app_login');
    }

    #[Route('/admin/dashboard', name: 'admin_dashboard')]
    public function adminDashboard(): Response
    {
        return $this->render('admin_dashboard.html.twig');
    }

    #[Route('/individual/dashboard', name: 'individual_dashboard')]
    public function individualDashboard(): Response
    {
        return $this->render('individual_dashboard.html.twig');
    }

    #[Route('/company/dashboard', name: 'company_dashboard')]
    public function companyDashboard(): Response
    {
        return $this->render('company_dashboard.html.twig');
    }
}
