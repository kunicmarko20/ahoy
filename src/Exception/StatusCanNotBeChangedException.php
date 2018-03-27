<?php

namespace App\Exception;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StatusCanNotBeChangedException extends BadRequestHttpException
{
    public function __construct()
    {
        parent::__construct('Status was already changed.');
    }
}
