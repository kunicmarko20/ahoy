<?php

namespace App\Validator\Constraints;

use App\Validator\IsMarkedAsSpamValidator;
use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class IsMarkedAsSpam extends Constraint
{
    public function getTargets()
    {
        return self::PROPERTY_CONSTRAINT;
    }

    public function validatedBy()
    {
        return IsMarkedAsSpamValidator::class;
    }
}
