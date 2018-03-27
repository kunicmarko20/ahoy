<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class InvalidStatusException extends BadRequestHttpException
{
    public function __construct(int $status)
    {
        parent::__construct("Status $status is invalid.");
    }
}
