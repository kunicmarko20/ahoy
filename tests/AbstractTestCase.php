<?php

namespace Tests\App;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\BrowserKit\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractTestCase extends WebTestCase
{
    protected static $container;

    protected function getContainer(): ContainerInterface
    {
        if (!self::$container) {
            return self::$container = static::createClient()->getContainer();
        }

        if (!self::$kernel->getContainer()) {
            self::$kernel->boot();
        }

        return self::$kernel->getContainer();
    }

    protected function get(string $serviceId)
    {
        return $this->getContainer()->get($serviceId);
    }

    protected function getManager(): EntityManagerInterface
    {
        return $this->getContainer()->get('doctrine')->getManager();
    }

    protected function find(string $className, int $id)
    {
        return $this->getManager()->find($className, $id);
    }

    protected function findOneBy(string $className, array $parameters = [])
    {
        return $this->getManager()
            ->getRepository($className)
            ->findOneBy($parameters);
    }

    protected function request(string $method, string $route, array $parameters = []): Client
    {
        $client = static::createClient();

        $client->request($method, $route, $parameters);

        return $client;
    }
}
