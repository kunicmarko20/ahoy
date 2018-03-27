<?php

namespace App\Repository;

use Doctrine\ORM\EntityManagerInterface;

trait RepositoryTrait
{
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function save($object): void
    {
        $this->entityManager->persist($object);
        $this->entityManager->flush();
    }

    public function remove($object): void
    {
        $this->entityManager->remove($object);
        $this->entityManager->flush();
    }
}
