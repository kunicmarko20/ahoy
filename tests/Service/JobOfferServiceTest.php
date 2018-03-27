<?php

namespace Tests\App\Service;

use App\Entity\JobOffer;
use App\Repository\JobOfferRepository;
use App\Service\JobOfferService;
use App\Service\MailMessageBuilder;
use App\Service\MailService;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use App\DTO;
use Tests\App\DataFixtures\JobOfferFixtures;

class JobOfferServiceTest extends TestCase
{
    private $mailService;
    private $jobOfferRepostitory;

    /**
     * @var JobOfferService
     */
    private $jobOfferService;

    protected function setUp()
    {
        $this->mailService = $this->prophesize(MailService::class);
        $this->jobOfferRepostitory = $this->prophesize(JobOfferRepository::class);

        $this->jobOfferService = new JobOfferService(
            $this->jobOfferRepostitory->reveal(),
            $this->mailService->reveal()
        );
    }

    public function testCreateFirst(): void
    {
        $this->jobOfferRepostitory->findIsFirstJobOffer($email = 'test email')->willReturn(true);
        $this->jobOfferRepostitory->save(Argument::type(JobOffer::class))->shouldBeCalledTimes(1);

        $messageBuilder = $this->prophesize(MailMessageBuilder::class);
        $messageBuilder->setTo(Argument::type('string'))->willReturn($messageBuilder);
        $messageBuilder->setTemplate(Argument::cetera())->willReturn($messageBuilder);
        $messageBuilder->setSubject(Argument::type('string'))->willReturn($messageBuilder);
        $messageBuilder->getMessage()->willReturn(new \Swift_Message())->shouldBeCalledTimes(2);

        $this->mailService->getMessageBuilder()->willReturn($messageBuilder)->shouldBeCalledTimes(2);
        $this->mailService->send(Argument::type(\Swift_Message::class))->shouldBeCalledTimes(2);

        $createJobOffer = new DTO\CreateJobOffer();

        $createJobOffer->title = 'test title';
        $createJobOffer->email = $email;
        $createJobOffer->description = 'test description';

        $this->jobOfferService->create($createJobOffer);
    }

    public function testCreateAdditional(): void
    {
        $this->jobOfferRepostitory->findIsFirstJobOffer($email = 'test email')->willReturn(false);
        $this->jobOfferRepostitory->save(Argument::type(JobOffer::class))->shouldBeCalledTimes(1);

        $createJobOffer = new DTO\CreateJobOffer();

        $createJobOffer->title = 'test title';
        $createJobOffer->email = $email;
        $createJobOffer->description = 'test description';

        $this->jobOfferService->create($createJobOffer);
    }

    public function testChangeStatus(): void
    {
        $jobOffer = JobOfferFixtures::createJobOffer();

        $this->jobOfferService->changeStatus($jobOffer, JobOffer::STATUS_APPROVED);

        $this->assertSame(JobOffer::STATUS_APPROVED, $jobOffer->getStatus());
    }

    /**
     * @expectedException \App\Exception\StatusCanNotBeChangedException
     * @expectedExceptionMessage Status was already changed.
     *
     * @dataProvider changeStatusExceptionData
     */
    public function testChangeStatusException(int $status): void
    {
        $jobOffer = JobOfferFixtures::createJobOffer(['status' => $status]);

        $this->jobOfferService->changeStatus($jobOffer, $status);
    }

    public function changeStatusExceptionData(): array
    {
        return [
          [JobOffer::STATUS_APPROVED],
          [JobOffer::STATUS_SPAM],
        ];
    }

    /**
     * @expectedException \App\Exception\InvalidStatusException
     * @expectedExceptionMessage Status 5 is invalid.
     */
    public function testChangeStatusInvalidStatus(): void
    {
        $jobOffer = JobOfferFixtures::createJobOffer(['status' => 0]);

        $this->jobOfferService->changeStatus($jobOffer, 5);
    }
}
