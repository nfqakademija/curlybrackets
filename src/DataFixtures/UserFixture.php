<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Common\Persistence\ObjectManager;

class UserFixture extends BaseFixtures
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(User::class, 10, function (User $user) {
            $user->setEmail($this->faker->email);
            $user->setFirstName($this->faker->firstName);
            $user->setLastName($this->faker->lastName);
            $user->setUsername($this->faker->userName);
            $user->setAvatar($this->faker->imageUrl(640, 480, 'cats'));
            $user->setCreatedAt($this->faker->dateTimeBetween('-200 days', '-100 days'));
            $user->setUpdatedAt($this->faker->dateTimeBetween('-100 days', '-1 days'));
        });
        $manager->flush();
    }
}
