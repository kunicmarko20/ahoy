<?php

namespace App\Validator;

use App\Repository\JobOfferRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsWaitingForReviewValidator extends ConstraintValidator
{
    /**
     * @var JobOfferRepository
     */
    private $jobOfferRepository;

    public function __construct(JobOfferRepository $jobOfferRepository)
    {
        $this->jobOfferRepository = $jobOfferRepository;
    }

    /**
     * @param ?string $email
     * @param Constraint $constraint
     */
    public function validate($email, Constraint $constraint)
    {
        if ($this->jobOfferRepository->findIsWaitingForReview($email)) {
            $this->context->buildViolation('You have to wait until the first job offer is approved.')
                ->atPath('email')
                ->addViolation();
        }
    }
}
