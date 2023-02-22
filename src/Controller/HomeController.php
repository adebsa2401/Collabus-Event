<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function home(): Response
    {
        return $this->render('dashboard.html.twig');
    }
}
