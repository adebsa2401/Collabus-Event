<?php

namespace App\Controller;

use App\Entity\EventParticipationRequest;
use App\Entity\EventType;
use App\Repository\ActivityAreaRepository;
use App\Repository\CompanyProfileRepository;
use App\Repository\CompanyRepository;
use App\Repository\EventParticipationRequestRepository;
use App\Repository\EventRepository;
use App\Repository\EventTypeRepository;
use App\Repository\IndividualProfileRepository;
use App\Repository\PlaceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(
        EventRepository $eventRepository,
        CompanyRepository $companyRepository,
        EventParticipationRequestRepository $eventParticipationRequestRepository,
        IndividualProfileRepository $individualProfileRepository,
        EventTypeRepository $eventTypeRepository,
    ): Response
    {
        $allEvents = $eventRepository->findAll();
        $events = $eventRepository->findUpcomingEvents();

        $companies = $companyRepository->findAll();
        $activityAreas = array_map(fn ($company) => $company->getActivityAreas()->toArray(), $companies);
        $activityAreas = array_map(fn ($activityArea) => $activityArea->getName(), array_merge(...$activityAreas));
        $activityAreas = array_unique($activityAreas);

        // Calculate global attendance rate
        $globalAttendanceRate = 0;
        $total = 0;
        foreach ($allEvents as $event) {
            $globalAttendanceRate += $event->getAttendances()->count() * $event->getAttendanceRate();
            $total += $event->getAttendances()->count();
        }
        $globalAttendanceRate = $total > 0 ? $globalAttendanceRate / $total : 0;

        // Calculate request approvement rate
        $totalRequests = array_sum(array_map(fn ($event) => $event->getParticipationRequests()->count(), $allEvents));
        $totalParticipations = array_sum(array_map(fn ($event) => $event->getAttendances()->count(), $allEvents));
        $globalAttendanceValidationRate = $totalRequests > 0 ? $totalParticipations / $totalRequests : 0;

        // calculate companies interest rate
        $totalRequestsByCompany = count(array_filter($eventParticipationRequestRepository->findAll(), fn (EventParticipationRequest $request) => $request->getCreatedBy()->getCompanyProfile()));
        $totalCompany = count($companyRepository->findAll());
        $companiesInterestRate = $totalCompany > 0 ? $totalRequestsByCompany / $totalCompany : 0;

        // calculate individual profiles interest rate
        $totalRequestsByIndividual = count(array_filter($eventParticipationRequestRepository->findAll(), fn (EventParticipationRequest $request) => $request->getCreatedBy()->getIndividualProfile()));
        $totalIndividual = count($individualProfileRepository->findAll());
        $individualsInterestRate = $totalIndividual > 0 ? $totalRequestsByIndividual / $totalIndividual : 0;

        $eventTypes = $eventTypeRepository->findAll();

        return $this->render('home/index.html.twig', compact(
            'events',
            'allEvents',
            'companies',
            'activityAreas',
            'globalAttendanceRate',
            'globalAttendanceValidationRate',
            'companiesInterestRate',
            'individualsInterestRate',
            'eventTypes'
        ));
    }

    #[Route('/public/event-type/{id}', name: 'app_home_event_type_show')]
    public function eventTypeShow(EventType $eventType): Response
    {
        return $this->render('home/event_type/show.html.twig', compact('eventType'));
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(
        CompanyRepository $companyRepository,
        EventRepository $eventRepository,
        CompanyProfileRepository $companyProfileRepository,
        IndividualProfileRepository $individualProfileRepository,
        EventTypeRepository $eventTypeRepository,
        PlaceRepository $placeRepository,
        ActivityAreaRepository $activityAreaRepository
    ): Response
    {
        return $this->render('dashboard.html.twig', [
            'companies' => $companyRepository->findAll(),
            'events' => $eventRepository->findAll(),
            'companyProfiles' => $companyProfileRepository->findAll(),
            'individualProfiles' => $individualProfileRepository->findAll(),
            'eventTypes' => $eventTypeRepository->findAll(),
            'places' => $placeRepository->findAll(),
            'activityAreas' => $activityAreaRepository->findAll(),
        ]);
    }
}
