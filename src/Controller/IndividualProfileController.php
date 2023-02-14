<?php

namespace App\Controller;

use App\Entity\IndividualProfile;
use App\Repository\IndividualProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/individual-profile')]
class IndividualProfileController extends AbstractController
{
    #[Route('/', name: 'app_individual_profile_index', methods: ['GET'])]
    public function index(IndividualProfileRepository $individualProfileRepository): Response
    {
        return $this->render('individual_profile/index.html.twig', [
            'individual_profiles' => $individualProfileRepository->findAll(),
        ]);
    }

    #[Route('/{id}', name: 'app_individual_profile_show', methods: ['GET'])]
    public function show(IndividualProfile $individualProfile): Response
    {
        return $this->render('individual_profile/show.html.twig', [
            'individual_profile' => $individualProfile,
        ]);
    }
}
