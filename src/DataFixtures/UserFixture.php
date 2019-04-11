<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends Fixture
{
    public function load(ObjectManager $manager)
    {





        $user = new User();
        $user->setEmail(sprintf('spacebar1@example.com'));
        $user->setUsername(sprintf('Name'));
        $manager->persist($user);
        $manager->flush();
    }
}
