<?php

namespace App\Validator;

use App\Repository\JobOfferRepository;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class IsMarkedAsSpamValidator extends ConstraintValidator
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
        if ($this->jobOfferRepository->findIsEmailMarkedAsSpam($email)) {
            $this->context->buildViolation('Job offers from this email are declined.')
                ->atPath('email')
                ->addViolation();
        }
    }
}
