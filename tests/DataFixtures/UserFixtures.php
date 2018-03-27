<?php

namespace Tests\App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $manager->persist(User::create(
            'moderator@mod.com',
            'moderator',
            'ROLE_MODERATOR'
        ));

        $manager->flush();
    }
}
