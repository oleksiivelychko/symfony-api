<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ApiFixtures extends Fixture
{
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        $group01 = new Group('group-01');
        $manager->persist($group01);

        $user = new User();
        $user->setName('user-01');
        $user->setEmail('user-01@email.com');
        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, 'secret')
        );
        $user->setRoles('USER');
        $user->addGroups($group01);
        $manager->persist($user);

        $group02 = new Group('group-02');
        $manager->persist($group02);

        $user = new User();
        $user->setName('user-02');
        $user->setEmail('user-02@email.com');
        $user->setPassword(
            $this->userPasswordHasher->hashPassword($user, 'secret')
        );
        $user->setRoles('USER');
        $user->addGroups($group01, $group02);
        $manager->persist($user);

        $manager->flush();
    }
}
