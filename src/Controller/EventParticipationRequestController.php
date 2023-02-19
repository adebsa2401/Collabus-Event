<?php

namespace App\Controller;

use App\Entity\EventParticipationRequest;
use App\Repository\EventParticipationRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event-participation-request')]
class EventParticipationRequestController extends AbstractController
{
    #[Route('/', name: 'app_event_participation_request_index', methods: ['GET'])]
    public function index(EventParticipationRequestRepository $eventParticipationRequestRepository): Response
    {
        return $this->render('event_participation_request/index.html.twig', [
            'event_participation_requests' => $eventParticipationRequestRepository->findAll(),
        ]);
    }

    #[Route('/company-profile', name: 'app_event_participation_request_index_by_company_profile', methods: ['GET'])]
    public function indexByCompanyProfile(EventParticipationRequestRepository $eventParticipationRequestRepository): Response
    {
        return $this->render('event_participation_request/index.html.twig', [
            'event_participation_requests' => $eventParticipationRequestRepository->findBy(['createdBy' => $this->getUser()]),
        ]);
    }

    #[Route('/individual-profile', name: 'app_event_participation_request_index_by_individual_profile', methods: ['GET'])]
    public function indexByIndividualProfile(EventParticipationRequestRepository $eventParticipationRequestRepository): Response
    {
        return $this->render('event_participation_request/index.html.twig', [
            'event_participation_requests' => $eventParticipationRequestRepository->findBy(['participant' => $this->getUser()->getIndividualProfile()]),
        ]);
    }

    #[Route('/{id}', name: 'app_event_participation_request_show', methods: ['GET'])]
    public function show(EventParticipationRequest $eventParticipationRequest): Response
    {
        return $this->render('event_participation_request/show.html.twig', [
            'event_participation_request' => $eventParticipationRequest,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_event_participation_request_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, EventParticipationRequest $eventParticipationRequest, EventParticipationRequestRepository $eventParticipationRequestRepository): Response
    {
        $isGet = $request->isMethod('GET');
        $isPost = $request->isMethod('POST') && $this->isCsrfTokenValid('delete'.$eventParticipationRequest->getId(), $request->request->get('_token'));

        if ($isGet || $isPost) {
            $eventParticipationRequestRepository->remove($eventParticipationRequest, true);
        }

        return $this->redirectToRoute('app_event_participation_request_index', [], Response::HTTP_SEE_OTHER);
    }
}