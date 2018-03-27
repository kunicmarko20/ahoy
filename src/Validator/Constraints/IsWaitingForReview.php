<?php

namespace App\Validator\Constraints;

use App\Validator\IsWaitingForReviewValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsWaitingForReview extends Constraint
{
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy()
    {
        return IsWaitingForReviewValidator::class;
    }
}
