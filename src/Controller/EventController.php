<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\EventParticipationRequest;
use App\Form\EventType;
use App\Form\ParticipateEventCompanyProfileType;
use App\Repository\EventParticipationRequestRepository;
use App\Repository\EventRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event')]
class EventController extends AbstractController
{
    #[Route('/', name: 'app_event_index', methods: ['GET'])]
    public function index(EventRepository $eventRepository): Response
    {
        return $this->render('event/index.html.twig', [
            'events' => $eventRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_new', methods: ['GET', 'POST'])]
    #[IsGranted('EVENT_CREATE')]
    public function new(Request $request, EventRepository $eventRepository): Response
    {
        $event = new Event();
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventRepository->save($event, true);

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/new.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/participate', name: 'app_event_participate', methods: ['GET', 'POST'])]
    public function participateEventCompanyProfile(Request $request, Event $event, EventRepository $eventRepository, EventParticipationRequestRepository $eventParticipationRequestRepository): Response
    {
        $collaborators = [];
        foreach ($this->getUser()->getCompanyProfile()->getCompanies() as $company) {
            $collaborators = array_merge($collaborators, $company->getCollaborators()->toArray());
        }
        $form = $this->createForm(ParticipateEventCompanyProfileType::class, null, [
            'collaborators' => $collaborators,
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            foreach ($form->get('participants')->getData() as $participant) {
                $request = (new EventParticipationRequest())
                    ->setEvent($event)
                    ->setParticipant($participant)
                    ->setCreatedBy($this->getUser())
                    ->setStatus(EventParticipationRequest::STATUS_PENDING)
                ;
                $eventParticipationRequestRepository->save($request, true);
            }

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/participate.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_show', methods: ['GET'])]
    #[IsGranted('EVENT_VIEW', subject: 'event')]
    public function show(Event $event): Response
    {
        return $this->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_edit', methods: ['GET', 'POST'])]
    #[IsGranted('EVENT_EDIT', subject: 'event')]
    public function edit(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $form = $this->createForm(EventType::class, $event);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventRepository->save($event, true);

            return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event/edit.html.twig', [
            'event' => $event,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_delete', methods: ['POST'])]
    #[IsGranted('EVENT_DELETE', subject: 'event')]
    public function delete(Request $request, Event $event, EventRepository $eventRepository): Response
    {
        $isGet = $request->isMethod('GET');
        $isPost = $request->isMethod('POST') && $this->isCsrfTokenValid('delete'.$event->getId(), $request->request->get('_token'));

        if ($isGet || $isPost) {
            $eventRepository->remove($event, true);
        }

        return $this->redirectToRoute('app_event_index', [], Response::HTTP_SEE_OTHER);
    }
}
