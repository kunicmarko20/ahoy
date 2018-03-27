<?php

namespace Tests\App\Controller;

use App\Entity\JobOffer;
use Symfony\Component\BrowserKit\Cookie;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Tests\App\AbstractTestCase;

class JobOfferControllerTest extends AbstractTestCase
{
    public function testCreate(): void
    {
        $client = static::createClient();

        $client->enableProfiler();

        $client->request(
            'POST',
            'job-offer/create',
            [
                'create_job_offer' =>  [
                    'title' => $title = 'test first',
                    'description' => 'test description',
                    'email' => $email = 'test123@gmail.com',
                ],
            ]
        );

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertSame(2, $mailCollector->getMessageCount());

        $collectedMessages = $mailCollector->getMessages();
        $messageOne = $collectedMessages[0];

        $this->assertSame($email, key($messageOne->getTo()));
        $messageTwo = $collectedMessages[1];

        $this->assertSame('kunicmarko20@gmail.com', key($messageTwo->getTo()));

        if ($client->getResponse()->isRedirection()) {
            $client->followRedirect();
        }

        $this->assertNotNull(
            $this->findOneBy(JobOffer::class, ['title' => $title])
        );

        $this->assertContains('Job Offer was created.', $client->getResponse()->getContent());
    }

    public function testCreateOfferNotApproved(): void
    {
        $client = $this->request(
            'POST',
            'job-offer/create',
            [
                'create_job_offer' =>  [
                    'title' => $title = 'test title',
                    'description' => 'test description',
                    'email' => 'waiting_for_review@test.com',
                ],
            ]
        );

        $this->assertNull(
            $this->findOneBy(JobOffer::class, ['title' => $title])
        );

        $this->assertContains(
            'You have to wait until the first job offer is approved.',
            $client->getResponse()->getContent()
        );
    }

    public function testCreateOfferMarkedAsSpam(): void
    {
        $client = $this->request(
            'POST',
            'job-offer/create',
            [
                'create_job_offer' =>  [
                    'title' => $title = 'test title',
                    'description' => 'test description',
                    'email' => 'spam@test.com',
                ],
            ]
        );

        $this->assertNull(
            $this->findOneBy(JobOffer::class, ['title' => $title])
        );

        $this->assertContains(
            'Job offers from this email are declined.',
            $client->getResponse()->getContent()
        );
    }

    public function testCreateOfferAlreadyApproved(): void
    {
        $client = static::createClient();

        $client->enableProfiler();

        $client->request(
            'POST',
            'job-offer/create',
            [
                'create_job_offer' =>  [
                    'title' => $title = 'test title',
                    'description' => 'test description',
                    'email' => 'approved@test.com',
                ],
            ]
        );

        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertSame(0, $mailCollector->getMessageCount());

        if ($client->getResponse()->isRedirection()) {
            $client->followRedirect();
        }

        $this->assertNotNull(
            $this->findOneBy(JobOffer::class, ['title' => $title])
        );

        $this->assertContains(
            'Job Offer was created.',
            $client->getResponse()->getContent()
        );
    }

    public function testChangeStatusNotLoggedIn(): void
    {
        $client = $this->request(
            'GET',
            'job-offer/1/approve'
        );

        if ($client->getResponse()->isRedirection()) {
            $client->followRedirect();
        }

        $this->assertContains(
            'email',
            $client->getResponse()->getContent()
        );

        $this->assertContains(
            'password',
            $client->getResponse()->getContent()
        );
    }

    public function testChangeStatus(): void
    {
        $client = static::createClient();

        $client->getCookieJar()->set($this->getLoginCookie());

        $client->request(
            'GET',
            'job-offer/1/approve'
        );

        $this->assertContains(
            'Job Offer status was changed.',
            $client->getResponse()->getContent()
        );
    }

    private function getLoginCookie(): Cookie
    {
        $session = $this->get('session');

        $session->set(
            '_security_main',
            serialize(new UsernamePasswordToken('moderator@mod.com', null, 'main', ['ROLE_MODERATOR']))
        );

        $session->save();

        return new Cookie($session->getName(), $session->getId());
    }

    public function testChangeStatusAlreadyChanged(): void
    {
        $client = static::createClient();

        $client->getCookieJar()->set($this->getLoginCookie());

        $client->request(
            'GET',
            'job-offer/3/spam'
        );

        $this->assertContains(
            'Status was already changed.',
            $client->getResponse()->getContent()
        );
    }
}
