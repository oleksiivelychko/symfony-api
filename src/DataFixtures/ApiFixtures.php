<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ApiFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $group01 = new Group('group-01');
        $manager->persist($group01);

        $user = new User('user-01', 'user-01@email.com', $group01);
        $manager->persist($user);

        $group02 = new Group('group-02');
        $manager->persist($group02);

        $user = new User('user-02', 'user-02@email.com', $group01, $group02);
        $manager->persist($user);

        $manager->flush();
    }
}
