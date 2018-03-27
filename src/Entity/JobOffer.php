<?php

namespace App\Entity;

use App\DTO;
use App\Exception\InvalidStatusException;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="job_offer")
 * @ORM\Entity
 */
class JobOffer
{
    public const STATUS_WAITING_FOR_REVIEW = 0;
    public const STATUS_APPROVED = 1;
    public const STATUS_SPAM = 2;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status = self::STATUS_WAITING_FOR_REVIEW;

    private function __construct(string $title, string $description, string $email)
    {
        $this->title = $title;
        $this->description = $description;
        $this->email = $email;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public static function fromCreateJobOffer(DTO\CreateJobOffer $createJobOffer): self
    {
        return new self(
            $createJobOffer->title,
            $createJobOffer->description,
            $createJobOffer->email
        );
    }

    public function approve(): void
    {
        $this->status = self::STATUS_APPROVED;
    }

    public function applyStatus(int $status): void
    {
        if ($status !== self::STATUS_APPROVED && $status !== self::STATUS_SPAM) {
            throw new InvalidStatusException($status);
        }

        $this->status = $status;
    }

    public function canChangeStatus(): bool
    {
        return $this->status === self::STATUS_WAITING_FOR_REVIEW;
    }
}
