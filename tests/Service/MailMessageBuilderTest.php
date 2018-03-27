<?php

namespace Tests\App\Service;

use App\Service\MailMessageBuilder;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

class MailMessageBuilderTest extends TestCase
{
    private $twig;

    /**
     * @var MailMessageBuilder
     */
    private $mailMessageBuilder;

    public function setUp()
    {
        $this->twig = $this->prophesize(Environment::class);

        $this->mailMessageBuilder = new MailMessageBuilder(
            'kunicmarko20@gmail.com',
            'kunicmarko20@gmail.com',
            $this->twig->reveal()
        );
    }

    public function testBuilder()
    {
        $this->mailMessageBuilder->setBody($body = 'testing')
            ->setSubject($subject = 'subject');

        $message = $this->mailMessageBuilder->getMessage();

        $this->assertInstanceOf(\Swift_Message::class, $message);
        $this->assertSame('kunicmarko20@gmail.com', key($message->getFrom()));
        $this->assertSame('kunicmarko20@gmail.com', key($message->getTo()));
        $this->assertSame($body, $message->getBody());
        $this->assertSame($subject, $message->getSubject());
    }

    public function testBuilderChangeTo()
    {
        $this->mailMessageBuilder->setTo($to = 'test@test.com');

        $message = $this->mailMessageBuilder->getMessage();

        $this->assertSame($to, key($message->getTo()));
    }
}
