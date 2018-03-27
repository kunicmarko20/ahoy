<?php

namespace App\EventSubscriber;

use App\Entity\User;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use function get_class;

class HashPasswordSubscriber implements EventSubscriber
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function getSubscribedEvents(): array
    {
        return ['prePersist', 'preUpdate'];
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $user = $args->getEntity();

        if (!$user instanceof User) {
            return;
        }
        
        $this->encodePassword($user);
    }

    public function preUpdate(LifecycleEventArgs $args): void
    {
        $user = $args->getEntity();

        if (!$user instanceof User) {
            return;
        }
        
        $this->encodePassword($user);

        $entityManager = $args->getEntityManager();
        $metaData = $entityManager->getClassMetadata(get_class($user));
        $entityManager->getUnitOfWork()->recomputeSingleEntityChangeSet($metaData, $user);
    }

    private function encodePassword(User $user): void
    {
        if (!$user->getPlainPassword()) {
            return;
        }

        $encoded = $this->passwordEncoder->encodePassword(
            $user,
            $user->getPlainPassword()
        );

        $user->setPassword($encoded);
    }
}
