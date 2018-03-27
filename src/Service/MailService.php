<?php

namespace App\Service;

use App\Util\LoggerTrait;

class MailService
{
    use LoggerTrait;

    /**
     * @var MailMessageBuilder
     */
    private $messageBuilder;

    /**
     * @var \Swift_Mailer
     */
    private $mailer;

    public function __construct(MailMessageBuilder $messageBuilder, \Swift_Mailer $mailer)
    {
        $this->messageBuilder = $messageBuilder;
        $this->mailer = $mailer;
    }

    public function getMessageBuilder(): MailMessageBuilder
    {
        return $this->messageBuilder->newInstance();
    }

    public function send(\Swift_Message $message) : void
    {
        try {
            $this->mailer->send($message);
        } catch (\Exception $exception) {
            $this->log($exception->getMessage());
        }
    }
}
