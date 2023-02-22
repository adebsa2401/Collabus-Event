<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventAttendance;
use App\Repository\EventAttendanceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event-attendance')]
class EventAttendanceController extends AbstractController
{
    #[Route('/{id}/check-qr-code', name: 'app_event_attendance_check_qr_code', methods: ['GET', 'POST'])]
    public function checkQrCode(Request $request, EventAttendance $eventAttendance, EventAttendanceRepository $eventAttendanceRepository): Response
    {
        if ($request->isMethod('POST')) {
            $event = $eventAttendance->getEvent();

            if (!$qrCode = $request->request->get('qr_code')) {
                $this->addFlash('error', 'Please upload a file');

                return $this->render('event_attendance/check_qr_code.html.twig', [
                    'event_attendance' => $eventAttendance,
                ]);
            }

            if ($qrCode !== $event->getQrCode()) {
                $this->addFlash('error', 'QR code is invalid');

                return $this->render('event_attendance/check_qr_code.html.twig', [
                    'event_attendance' => $eventAttendance,
                ]);
            }

            $eventAttendance
                ->setIsVerified(true)
                ->setIsVerifiedAt(new \DateTimeImmutable())
            ;

            $eventAttendanceRepository->save($eventAttendance, true);

            $this->addFlash('success', 'QR code is valid');
            return $this->redirectToRoute('app_event_show', ['id' => $event->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('event_attendance/check_qr_code.html.twig', [
            'event_attendance' => $eventAttendance,
        ]);
    }

    #[Route('/', name: 'app_event_attendance_index', methods: ['GET'])]
    public function index(EventAttendanceRepository $eventAttendanceRepository): Response
    {
        if ($this->isGranted('ROLE_INDIVIDUAL')) {
            return $this->render('event_attendance/index.html.twig', [
                'event_attendances' => $eventAttendanceRepository->findBy(['participant' => $this->getUser()->getIndividualProfile()]),
            ]);
        } elseif ($this->isGranted('ROLE_COMPANY')) {
            return $this->render('event_attendance/index.html.twig', [
                'event_attendances' => $eventAttendanceRepository->findBy(['representedCompany' => $this->getUser()->getCompanyProfile()]),
            ]);
        }
    }
    #[Route('/', name: 'app_event_attendance_by_individual_profile_index', methods: ['GET'])]
    public function indexByIndividualProfile(EventAttendanceRepository $eventAttendanceRepository): Response
    {
        return $this->render('event_attendance/index.html.twig', [
            'event_attendances' => $eventAttendanceRepository->findBy(['participant' => $this->getUser()->getIndividualProfile()]),
        ]);
    }

    #[Route('/event/{id}', name: 'app_event_attendance_by_company_profile_index', methods: ['GET'])]
    public function indexByCompanyProfile(EventAttendanceRepository $eventAttendanceRepository, Event $event): Response
    {
        return $this->render('event_attendance/index.html.twig', [
            'event_attendances' => $eventAttendanceRepository->findBy(['event' => $event, 'representedCompany' => $this->getUser()->getCompanyProfile()]),
        ]);
    }
}
