<?php

namespace App\Controller;

use App\DTO;
use App\Entity\JobOffer;
use App\Exception\StatusCanNotBeChangedException;
use App\Form\CreateJobOfferType;
use App\Service\JobOfferService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class JobOfferController extends Controller
{
    public function create(Request $request, JobOfferService $jobOfferService)
    {
        $form = $this->createForm(CreateJobOfferType::class, $createJobOffer = new DTO\CreateJobOffer());

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $jobOfferService->create($createJobOffer);

            $this->addFlash('success', 'Job Offer was created.');

            return $this->redirectToRoute('app_job_offer_create');
        }

        return $this->render('JobOffer/create.html.twig', ['form' => $form->createView()]);
    }

    public function changeStatus(
        JobOffer $jobOffer,
        int $status,
        JobOfferService $jobOfferService,
        LoggerInterface $logger
    ) {
        try {
            $jobOfferService->changeStatus($jobOffer, $status);
            $this->addFlash('success', 'Job Offer status was changed.');
        } catch (StatusCanNotBeChangedException $exception) {
            $logger->critical($exception->getMessage(), ['jobOffer' => $jobOffer]);
            $this->addFlash('danger', $exception->getMessage());
        }

        return $this->render('JobOffer/change_status.html.twig');
    }
}
