<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use App\Repository\EventRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function home(EventRepository $eventRepository, CompanyRepository $companyRepository): Response
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

        // Calculate attendance rate over last 3 events
        $attendanceRateOverLast3Events = 0;
        $total = 0;
        foreach (array_slice($allEvents, -3) as $event) {
            $attendanceRateOverLast3Events += $event->getAttendances()->count() * $event->getAttendanceRate();
            $total += $event->getAttendances()->count();
        }
        $attendanceRateOverLast3Events = $total > 0 ? $attendanceRateOverLast3Events / $total : 0;

        $highestAttendanceRate = 0;
        $lowestAttendanceRate = 0;
        if (count($allEvents) > 0) {
            $highestAttendanceRate = max(array_map(fn ($event) => $event->getAttendanceRate(), $allEvents));
            $lowestAttendanceRate = min(array_map(fn ($event) => $event->getAttendanceRate(), $allEvents));
        }

        return $this->render('index.html.twig', compact(
            'events',
            'allEvents',
            'companies',
            'activityAreas',
            'globalAttendanceRate',
            'attendanceRateOverLast3Events',
            'highestAttendanceRate',
            'lowestAttendanceRate'
        ));
    }

    #[Route('/dashboard', name: 'app_dashboard')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function dashboard(): Response
    {
        return $this->render('dashboard.html.twig');
    }
}
