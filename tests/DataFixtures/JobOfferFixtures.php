<?php

namespace Tests\App\DataFixtures;

use App\Entity\JobOffer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class JobOfferFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $jobOffer = self::createJobOffer([
            'title' => 'test waiting',
            'description' => 'test description',
            'email' => 'waiting_for_review@test.com',
        ]);
        $manager->persist($jobOffer);

        $jobOffer = self::createJobOffer([
            'title' => 'test spam',
            'description' => 'test description',
            'email' => 'spam@test.com',
            'status' => JobOffer::STATUS_SPAM
        ]);
        $manager->persist($jobOffer);

        $jobOffer = self::createJobOffer([
            'title' => 'test approved',
            'description' => 'test description',
            'email' => 'approved@test.com',
            'status' => JobOffer::STATUS_APPROVED
        ]);
        $manager->persist($jobOffer);

        $manager->flush();
    }

    public static function createJobOffer(array $values = []): JobOffer
    {
        $class = new \ReflectionClass(JobOffer::class);
        $jobOffer = $class->newInstanceWithoutConstructor();

        foreach ($values as $name => $value) {
            ($property = $class->getProperty($name))->setAccessible(true);
            $property->setValue($jobOffer, $value);
        }

        return $jobOffer;
    }
}
