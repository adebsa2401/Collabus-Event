<?php

namespace App\Controller;

use App\Entity\EventType;
use App\Form\EventTypeType;
use App\Repository\EventTypeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/event_type-type')]
class EventTypeController extends AbstractController
{
    #[Route('/', name: 'app_event_type_index', methods: ['GET'])]
    public function index(EventTypeRepository $eventTypeRepository): Response
    {
        return $this->render('event_type/index.html.twig', [
            'event_types' => $eventTypeRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_event_type_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EventTypeRepository $eventTypeRepository): Response
    {
        $eventType = new EventType();
        $form = $this->createForm(EventTypeType::class, $eventType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventTypeRepository->save($eventType, true);

            return $this->redirectToRoute('app_event_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event_type/new.html.twig', [
            'event_type' => $eventType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_type_show', methods: ['GET'])]
    public function show(EventType $eventType): Response
    {
        return $this->render('event_type/show.html.twig', [
            'event_type' => $eventType,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_event_type_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, EventType $eventType, EventTypeRepository $eventTypeRepository): Response
    {
        $form = $this->createForm(EventTypeType::class, $eventType);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $eventTypeRepository->save($eventType, true);

            return $this->redirectToRoute('app_event_type_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('event_type/edit.html.twig', [
            'event_type' => $eventType,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_event_type_delete', methods: ['POST'])]
    public function delete(Request $request, EventType $eventType, EventTypeRepository $eventTypeRepository): Response
    {
        $isGet = $request->isMethod('GET');
        $isPost = $request->isMethod('POST') && $this->isCsrfTokenValid('delete'.$eventType->getId(), $request->request->get('_token'));

        if ($isGet || $isPost) {
            $eventTypeRepository->remove($eventType, true);
        }

        return $this->redirectToRoute('app_event_type_index', [], Response::HTTP_SEE_OTHER);
    }
}
