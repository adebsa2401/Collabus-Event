<?php

namespace App\Controller;

use App\Entity\EventParticipationRequest;
use App\Repository\CompanyRepository;
use App\Repository\EventParticipationRequestRepository;
use App\Repository\EventRepository;
use App\Repository\IndividualProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(EventRepository $eventRepository, CompanyRepository $companyRepository, EventParticipationRequestRepository $eventParticipationRequestRepository, IndividualProfileRepository $individualProfileRepository): Response
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

        return $this->render('index.html.twig', compact(
            'events',
            'allEvents',
            'companies',
            'activityAreas',
            'globalAttendanceRate',
            'globalAttendanceValidationRate',
            'companiesInterestRate',
            'individualsInterestRate'
        ));
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    public function dashboard(): Response
    {
        return $this->render('dashboard.html.twig');
    }
}
