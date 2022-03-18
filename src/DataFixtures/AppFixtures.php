<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $encoder;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager): void
    {
        $admin = (new Admin())->setEmail("toto@toto.fr")->setRoles([ "ROLE_ADMIN", "ROLE_MENTOR"])->setFirstname("toto")->setLastname("toto");
        $hashPassword = $this->encoder->hashPassword($admin, "toto");
        $admin->setPassword($hashPassword);
        $manager->persist($admin);
        
        $mentor1 = (new User())->setEmail("mentor@mentor.fr")->setRoles([ "ROLE_MENTOR" ])->setFirstname("mentor")->setLastname("mentor");
        $hashPassword = $this->encoder->hashPassword($mentor1, "mentor");
        $mentor1->setPassword($hashPassword);        
        $manager->persist($mentor1);
        
        $user = (new User())->setEmail("user@user.fr")->setRoles([ "ROLE_HIGH_SCHOOL" ])->setFirstname("user")->setLastname("user")->setMentor($mentor1);
        $hashPassword = $this->encoder->hashPassword($user, "user");
        $user->setPassword($hashPassword);
        $manager->persist($user);
    

        $manager->flush();
    }
}
