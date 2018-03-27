<?php

namespace App\Util;

use Psr\Log\LoggerInterface;

trait LoggerTrait
{
    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @required
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    private function log(?string $message, array $context = []): void
    {
        if ($this->logger) {
            $this->logger->critical($message, $context);
        }
    }
}
