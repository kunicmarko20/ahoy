<?php

namespace App\Repository;

use App\Entity\JobOffer;

class JobOfferRepository
{
    use RepositoryTrait;

    public function findIsFirstJobOffer(string $email): bool
    {
        $query = $this->entityManager
            ->createQuery('SELECT jo FROM App:JobOffer jo WHERE jo.email = :email')
            ->setParameter('email', $email);

        return empty($query->getResult());
    }

    public function findIsEmailMarkedAsSpam(?string $email): bool
    {
        $query = $this->entityManager
            ->createQuery('SELECT jo FROM App:JobOffer jo WHERE jo.email = :email AND jo.status = :status')
            ->setParameters([
                'email' => $email,
                'status' => JobOffer::STATUS_SPAM
            ]);

        return !empty($query->getResult());
    }

    public function findIsWaitingForReview(?string $email): bool
    {
        $query = $this->entityManager
            ->createQuery('SELECT jo FROM App:JobOffer jo WHERE jo.email = :email AND jo.status = :status')
            ->setParameters([
                'email' => $email,
                'status' => JobOffer::STATUS_WAITING_FOR_REVIEW
            ]);

        return !empty($query->getResult());
    }
}
