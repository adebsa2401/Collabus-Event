<?php

namespace App\Controller;

use App\Entity\CompanyProfile;
use App\Repository\CompanyProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/company-profile')]
class CompanyProfileController extends AbstractController
{
    #[Route('/', name: 'app_company_profile_index', methods: ['GET'])]
    public function index(CompanyProfileRepository $companyProfileRepository): Response
    {
        return $this->render('company_profile/index.html.twig', [
            'company_profiles' => $companyProfileRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_company_profile_show', methods: ['GET'])]
    public function show(CompanyProfile $companyProfile): Response
    {
        return $this->render('company_profile/show.html.twig', [
            'company_profile' => $companyProfile,
        ]);
    }
}
