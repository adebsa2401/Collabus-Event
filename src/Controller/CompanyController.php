<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\CompanyProfile;
use App\Entity\IndividualProfile;
use App\Entity\User;
use App\Form\CompanyAddCollaboratorType;
use App\Form\CompanyType;
use App\Repository\CompanyRepository;
use App\Repository\IndividualProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/company')]
class CompanyController extends AbstractController
{
    #[Route('/', name: 'app_company_index', methods: ['GET'])]
    public function index(CompanyRepository $companyRepository): Response
    {
        return $this->render('company/index.html.twig', [
            'companies' => $companyRepository->findAll(),
        ]);
    }

    #[Route('/my-companies', name: 'app_company_by_company_profile_index', methods: ['GET'])]
    public function indexByCompanyProfile(): Response
    {   
        return $this->render('company/index.html.twig', [
            'companies' => $this->getUser()->getCompanyProfile()->getCompanies(),
        ]);
    }

    #[Route('/new', name: 'app_company_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CompanyRepository $companyRepository): Response
    {
        $company = (new Company())
            ->setOwner($this->getUser()->getCompanyProfile());
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $companyRepository->save($company, true);

            return $this->redirectToRoute('app_company_by_company_profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('company/new.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/collaborators', name: 'app_company_collaborators_index', methods: ['GET'])]
    public function collaboratorsIndex(): Response
    {
        $collaborators = [];
        foreach ($this->getUser()->getCompanyProfile()->getCompanies() as $company) {
            $collaborators = array_merge($collaborators, $company->getCollaborators()->toArray());
        }
        return $this->render('company/collaborators_index.html.twig', [
            'collaborators' => $collaborators,
        ]);
    }

    #[Route('/{id}/add-collaborators', name: 'app_company_add_collaborators', methods: ['GET', 'POST'])]
    public function addCollaborators(Request $request, Company $company, CompanyRepository $companyRepository, IndividualProfileRepository $individualProfileRepository): Response
    {
        $form = $this->createForm(CompanyAddCollaboratorType::class, null, [
            'individualProfiles' => array_filter(
                $individualProfileRepository->findAll(),
                fn (IndividualProfile $individualProfile) => !$individualProfile->getCompany()
            ),
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $form->get('collaborators')->getData()->map(fn (IndividualProfile $individualProfile) => $company->addCollaborator($individualProfile));
            $companyRepository->save($company, true);

            return $this->redirectToRoute('app_company_show', ['id' => $company->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('company/add_collaborators.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/remove-collaborator/{id}', name: 'app_company_remove_collaborator', methods: ['GET', 'POST'])]
    public function removeCollaborator(Request $request, IndividualProfileRepository $individualProfileRepository, IndividualProfile $individualProfile): Response
    {
        $isGet = $request->isMethod('GET');
        $isPost = $request->isMethod('POST') && $this->isCsrfTokenValid('delete'.$individualProfile->getId(), $request->request->get('_token'));
        $company = $individualProfile->getCompany();
        
        if ($isGet || $isPost) {
            $individualProfile->setCompany(null);
            $individualProfileRepository->save($individualProfile, true);
        }

        return $this->redirectToRoute('app_company_show', ['id' => $company->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_company_show', methods: ['GET'])]
    public function show(Company $company): Response
    {
        return $this->render('company/show.html.twig', [
            'company' => $company,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_company_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Company $company, CompanyRepository $companyRepository): Response
    {
        $form = $this->createForm(CompanyType::class, $company);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $companyRepository->save($company, true);

            return $this->redirectToRoute('app_company_by_company_profile_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('company/edit.html.twig', [
            'company' => $company,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_company_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request, Company $company, CompanyRepository $companyRepository): Response
    {
        $isGet = $request->isMethod('GET');
        $isPost = $request->isMethod('POST') && $this->isCsrfTokenValid('delete'.$company->getId(), $request->request->get('_token'));

        if ($isGet || $isPost) {
            $companyRepository->remove($company, true);
        }

        return $this->redirectToRoute('app_company_by_company_profile_index', [], Response::HTTP_SEE_OTHER);
    }
}
