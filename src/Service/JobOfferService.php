<?php

namespace App\Service;

use App\Entity\JobOffer;
use App\Exception\StatusCanNotBeChangedException;
use App\Repository\JobOfferRepository;
use App\DTO;

class JobOfferService
{
    /**
     * @var JobOfferRepository
     */
    private $jobOfferRepository;

    /**
     * @var MailService
     */
    private $mailService;

    public function __construct(JobOfferRepository $jobOfferRepository, MailService $mailService)
    {
        $this->jobOfferRepository = $jobOfferRepository;
        $this->mailService = $mailService;
    }

    public function create(DTO\CreateJobOffer $createJobOffer): void
    {
        $jobOffer = JobOffer::fromCreateJobOffer($createJobOffer);

        if ($this->jobOfferRepository->findIsFirstJobOffer($createJobOffer->email)) {
            $this->jobOfferRepository->save($jobOffer);
            $this->sendNotificationToUser($createJobOffer->email);
            $this->sendNotificationToModerator($jobOffer);
            return;
        }

        $jobOffer->approve();
        $this->jobOfferRepository->save($jobOffer);
    }

    private function sendNotificationToUser(string $email): void
    {
        $messageBuilder = $this->mailService->getMessageBuilder();

        $messageBuilder->setTo($email)
            ->setTemplate('Mail/user_notification.html.twig')
            ->setSubject('In Review');

        $this->mailService->send($messageBuilder->getMessage());
    }

    private function sendNotificationToModerator(JobOffer $jobOffer): void
    {
        $messageBuilder = $this->mailService->getMessageBuilder();

        $messageBuilder
            ->setTemplate('Mail/moderator_notification.html.twig', ['jobOffer' => $jobOffer])
            ->setSubject('New First Job Offer');

        $this->mailService->send($messageBuilder->getMessage());
    }

    public function changeStatus(JobOffer $jobOffer, int $status): void
    {
        if (!$jobOffer->canChangeStatus()) {
            throw new StatusCanNotBeChangedException();
        }

        $jobOffer->applyStatus($status);
        $this->jobOfferRepository->save($jobOffer);
    }
}
