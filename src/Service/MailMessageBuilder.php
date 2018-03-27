<?php

namespace App\Service;

use Twig\Environment;

class MailMessageBuilder
{
    /**
     * @var string
     */
    private $from;

    /**
     * @var string
     */
    private $to;

    /**
     * @var Environment
     */
    private $twig;

    /**
     * @var string
     */
    private $subject;

    /**
     * @var string
     */
    private $body;

    /**
     * @var string
     */
    private $contentType;

    public function __construct(string $from, string $to, Environment $twig)
    {
        $this->from = $from;
        $this->to = $to;
        $this->twig = $twig;
    }

    public function setSubject(string $subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function setFrom(string $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function setTo(string $to): self
    {
        $this->to = $to;

        return $this;
    }

    public function setBody(string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function setTemplate(string $template, array $options = []): self
    {
        $this->body = $this->twig->render($template, $options);
        $this->contentType = 'text/html';

        return $this;
    }

    public function setContentType(string $contentType): self
    {
        $this->contentType = $contentType;

        return $this;
    }

    public function getMessage(): \Swift_Message
    {
        return (new \Swift_Message($this->subject, $this->body, $this->contentType))
            ->setFrom($this->from)
            ->setTo($this->to);
    }

    public function newInstance(): self
    {
        return new self($this->from, $this->to, $this->twig);
    }
}
