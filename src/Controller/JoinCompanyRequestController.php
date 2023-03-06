<?php

namespace App\Controller;

use App\Entity\EventAttendance;
use App\Entity\JoinCompanyRequest;
use App\Form\JoinCompanyRequestType;
use App\Repository\JoinCompanyRequestRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/join-company-request')]
class JoinCompanyRequestController extends AbstractController
{
    #[Route('/', name: 'app_join_company_request_index', methods: ['GET'])]
    public function index(JoinCompanyRequestRepository $joinCompanyRequestRepository): Response
    {
        return $this->render('join_company_request/index.html.twig', [
            'sent_join_company_requests' => $joinCompanyRequestRepository->findBy(['requestedBy' => $this->getUser()->getCompanyProfile()]),
            'received_join_company_requests' => $joinCompanyRequestRepository->findBy(['requestedTo' => $this->getUser()->getCompanyProfile()]),
        ]);
    }

    #[Route('/{id}/new', name: 'app_join_company_request_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EventAttendance $eventAttendance, JoinCompanyRequestRepository $joinCompanyRequestRepository): Response
    {
        $joinCompanyRequest = (new JoinCompanyRequest())
            ->setEvent($eventAttendance->getEvent())
            ->setRequestedBy($this->getUser()->getCompanyProfile())
            ->setRequestedTo($eventAttendance->getRepresentedCompany())
            ->setStatus(JoinCompanyRequest::STATUS_PENDING)
        ;
        $form = $this->createForm(JoinCompanyRequestType::class, $joinCompanyRequest);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($joinCompanyRequestRepository->findRelations($joinCompanyRequest->getStartedAt(), $joinCompanyRequest->getEndedAt())) {
                $this->addFlash('danger', 'Une réunion est déjà prévue à cette date.');

                return $this->redirectToRoute('app_join_company_request_new', ['id' => $eventAttendance->getId()], Response::HTTP_SEE_OTHER);
            }
            $joinCompanyRequest
                ->setRequestedAt(new \DateTimeImmutable())
            ;
            $joinCompanyRequestRepository->save($joinCompanyRequest, true);

            return $this->redirectToRoute('app_join_company_request_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('join_company_request/new.html.twig', [
            'join_company_request' => $joinCompanyRequest,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/accept', name: 'app_join_company_request_accept', methods: ['GET', 'POST'])]
    public function accept(JoinCompanyRequest $joinCompanyRequest, JoinCompanyRequestRepository $joinCompanyRequestRepository): Response
    {
        $joinCompanyRequest
            ->setStatus(JoinCompanyRequest::STATUS_ACCEPTED)
        ;
        $joinCompanyRequestRepository->save($joinCompanyRequest, true);

        return $this->redirectToRoute('app_join_company_request_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/reject', name: 'app_join_company_request_reject', methods: ['GET', 'POST'])]
    public function reject(JoinCompanyRequest $joinCompanyRequest, JoinCompanyRequestRepository $joinCompanyRequestRepository): Response
    {
        $joinCompanyRequest
            ->setStatus(JoinCompanyRequest::STATUS_REJECTED)
        ;
        $joinCompanyRequestRepository->save($joinCompanyRequest, true);

        return $this->redirectToRoute('app_join_company_request_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_join_company_request_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, JoinCompanyRequest $joinCompanyRequest, JoinCompanyRequestRepository $joinCompanyRequestRepository): Response
    {
        $isGet = $request->isMethod('GET');
        $isPost = $request->isMethod('POST') && $this->isCsrfTokenValid('delete'.$joinCompanyRequest->getId(), $request->request->get('_token'));

        if ($isGet || $isPost) {
            $joinCompanyRequestRepository->remove($joinCompanyRequest, true);
        }

        return $this->redirectToRoute('app_join_company_request_index', [], Response::HTTP_SEE_OTHER);
    }
}
