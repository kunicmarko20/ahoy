<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as AppAssert;

class CreateJobOffer
{
    /**
     * @Assert\NotNull
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "Your title must be at least {{ limit }} characters long",
     *      maxMessage = "Your title cannot be longer than {{ limit }} characters"
     * )
     *
     * @var string
     */
    public $title;

    /**
     * @Assert\NotNull
     *
     * @var string
     */
    public $description;

    /**
     * @Assert\NotNull
     * @Assert\Email(strict=true)
     * @Assert\Length(
     *      min = 2,
     *      max = 255,
     *      minMessage = "Your email must be at least {{ limit }} characters long",
     *      maxMessage = "Your email cannot be longer than {{ limit }} characters"
     * )
     *
     * @AppAssert\IsMarkedAsSpam
     * @AppAssert\IsWaitingForReview
     *
     * @var string
     */
    public $email;
}
