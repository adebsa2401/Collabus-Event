<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Event;
use App\Entity\EventParticipationRequest;
use App\Entity\EventType;
use App\Repository\ActivityAreaRepository;
use App\Repository\CompanyProfileRepository;
use App\Repository\CompanyRepository;
use App\Repository\EventAttendanceRepository;
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
    #[Route('/', name: 'app_home', methods: ['GET'])]
    public function home(
        EventRepository $eventRepository,
        CompanyRepository $companyRepository,
        EventParticipationRequestRepository $eventParticipationRequestRepository,
        IndividualProfileRepository $individualProfileRepository,
        EventTypeRepository $eventTypeRepository,
        ActivityAreaRepository $activityAreaRepository
    ): Response
    {
        $allEvents = $eventRepository->findAll();
        $events = $eventRepository->findUpcomingEvents();

        $companies = $companyRepository->findAll();
        $activityAreas = $activityAreaRepository->findAll();
//        $activityAreas = array_map(fn ($company) => $company->getActivityAreas()->toArray(), $companies);
//        $activityAreas = array_map(fn ($activityArea) => $activityArea->getName(), array_merge(...$activityAreas));
//        $activityAreas = array_unique($activityAreas);

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

    #[Route('/public/event-type/{id}', name: 'app_home_event_type_show', methods: ['GET'])]
    public function eventTypeShow(EventType $eventType, EventRepository $eventRepository): Response
    {
        $events = $eventRepository->findUpcomingEvents();
        return $this->render('home/event_type/show.html.twig', compact('eventType', 'events'));
    }

    #[Route('/public/event/{id}', name: 'app_home_event_show', methods: ['GET'])]
    public function eventShow(Event $event, EventParticipationRequestRepository $eventParticipationRequestRepository, CompanyRepository $companyRepository, IndividualProfileRepository $individualProfileRepository): Response
    {
        // Calculate global attendance rate
        $globalAttendanceRate = $event->getAttendanceRate();

        // Calculate request approvement rate
        $totalRequests = $event->getParticipationRequests()->count();
        $totalParticipations = $event->getAttendances()->count();
        $globalAttendanceValidationRate = $totalRequests > 0 ? $totalParticipations / $totalRequests : 0;

        // calculate companies interest rate
        $totalRequestsByCompany = count(array_filter($eventParticipationRequestRepository->findBy(['event' => $event]), fn (EventParticipationRequest $request) => $request->getCreatedBy()->getCompanyProfile()));
        $totalCompany = count($companyRepository->findAll());
        $companiesInterestRate = $totalCompany > 0 ? $totalRequestsByCompany / $totalCompany : 0;

        // calculate individual profiles interest rate
        $totalRequestsByIndividual = count(array_filter($eventParticipationRequestRepository->findBy(['event' => $event]), fn (EventParticipationRequest $request) => $request->getCreatedBy()->getIndividualProfile()));
        $totalIndividual = count($individualProfileRepository->findAll());
        $individualsInterestRate = $totalIndividual > 0 ? $totalRequestsByIndividual / $totalIndividual : 0;

        return $this->render('home/event/show.html.twig', compact(
            'event',
            'globalAttendanceRate',
            'globalAttendanceValidationRate',
            'companiesInterestRate',
            'individualsInterestRate',
        ));
    }

    #[Route('/public/company/{id}', name: 'app_home_company_show', methods: ['GET'])]
    public function companyShow(Company $company, EventParticipationRequestRepository $eventParticipationRequestRepository, EventRepository $eventRepository, EventAttendanceRepository $eventAttendanceRepository): Response
    {
        $participationRequests = $eventParticipationRequestRepository->findBy(['createdBy' => $company->getOwner()->getUser()]);
        $participationRequestsCount = count($participationRequests);
        $participatedEvents = array_filter($participationRequests, fn (EventParticipationRequest $request) => $request->getStatus() === EventParticipationRequest::STATUS_ACCEPTED);
        $participatedEventsCount = count(array_unique(array_map(fn (EventParticipationRequest $request) => $request->getEvent()->getName(), $participatedEvents)));

        $eventsCount = count($eventRepository->findAll());
        $interestRate = $eventsCount > 0 ? $participationRequestsCount / $eventsCount : 0;
        $interestRate = min($interestRate, 1);

        $attendancesCount = count($eventAttendanceRepository->findBy(['representedCompany' => $company]));
        $verifiedAttendancesCount = count($eventAttendanceRepository->findBy(['representedCompany' => $company, 'isVerified' => true]));
        $attendanceRate = $attendancesCount > 0 ? $verifiedAttendancesCount / $attendancesCount : 0;
        $attendanceRate = min($attendanceRate, 1);

        $approvedParticipationRequests = array_filter($participationRequests, fn (EventParticipationRequest $request) => $request->getStatus() === EventParticipationRequest::STATUS_ACCEPTED);
        $approvedParticipationRequestsCount = count($approvedParticipationRequests);
        $participationRate = $participationRequestsCount > 0 ? $approvedParticipationRequestsCount / $participationRequestsCount : 0;
        $participationRate = min($participationRate, 1);

        return $this->render('home/company/show.html.twig', compact(
            'company',
            'participatedEventsCount',
            'participationRequestsCount',
            'interestRate',
            'attendanceRate',
            'participationRate'
        ));
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
