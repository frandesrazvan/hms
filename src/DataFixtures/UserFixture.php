<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Entity\User;

class UserFixture extends Fixture
{
    public function __construct(private UserPasswordHasherInterface $encoder)
    {
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $password = $this->encoder->hashPassword($user, 'Qwerty1!');
        $user->setUsername('owner')
             ->setPassword($password)
             ->setRoles(['OWNER'])
             ->setEmail('owner123@gmail.com')
             ->setIsActive(true);

        $manager->persist($user);
        $manager->flush();
    }
}
