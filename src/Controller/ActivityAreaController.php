<?php

namespace App\Controller;

use App\Entity\ActivityArea;
use App\Form\ActivityAreaType;
use App\Repository\ActivityAreaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/activity-area')]
class ActivityAreaController extends AbstractController
{
    #[Route('/', name: 'app_activity_area_index', methods: ['GET'])]
    public function index(ActivityAreaRepository $activityAreaRepository): Response
    {
        return $this->render('activity_area/index.html.twig', [
            'activity_areas' => $activityAreaRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_activity_area_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ActivityAreaRepository $activityAreaRepository): Response
    {
        $activityArea = new ActivityArea();
        $form = $this->createForm(ActivityAreaType::class, $activityArea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activityAreaRepository->save($activityArea, true);

            return $this->redirectToRoute('app_activity_area_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activity_area/new.html.twig', [
            'activity_area' => $activityArea,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_activity_area_show', methods: ['GET'])]
    public function show(ActivityArea $activityArea): Response
    {
        return $this->render('activity_area/show.html.twig', [
            'activity_area' => $activityArea,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_activity_area_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, ActivityArea $activityArea, ActivityAreaRepository $activityAreaRepository): Response
    {
        $form = $this->createForm(ActivityAreaType::class, $activityArea);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activityAreaRepository->save($activityArea, true);

            return $this->redirectToRoute('app_activity_area_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activity_area/edit.html.twig', [
            'activity_area' => $activityArea,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_activity_area_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, ActivityArea $activityArea, ActivityAreaRepository $activityAreaRepository): Response
    {
        $isGet = $request->isMethod('GET');
        $isPost = $request->isMethod('POST') && $this->isCsrfTokenValid('delete'.$activityArea->getId(), $request->request->get('_token'));

        if ($isGet || $isPost) {
            $activityAreaRepository->remove($activityArea, true);
        }

        return $this->redirectToRoute('app_activity_area_index', [], Response::HTTP_SEE_OTHER);
    }
}
