<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

        $user = new User();
        $user
            ->setFirstName("John")
            ->setLastName("Doe")
            ->setDateOfBirth(new \DateTime())
            ->setUsername("admin")
            ->setEmail("amdin@qqchose.fr")
            ->setPassword($this->encoder->encodePassword($user, "admin"))
            ->setGender(1)
            ->setCreatedAt(new \DateTime())
            ->setRoles(["ROLE_ADMIN"])
        ;
        $manager->persist($user);
        $user = new User();
        $user
            ->setFirstName("John")
            ->setLastName("Doe")
            ->setDateOfBirth(new \DateTime())
            ->setUsername("super_admin")
            ->setEmail("amdin@qqchose.fr")
            ->setPassword($this->encoder->encodePassword($user, "super_admin"))
            ->setGender(1)
            ->setCreatedAt(new \DateTime())
            ->setRoles(["ROLE_SUPER_ADMIN"])
        ;
        $manager->persist($user);
        $user = new User();
        $user
            ->setFirstName("John")
            ->setLastName("Doe")
            ->setDateOfBirth(new \DateTime())
            ->setUsername("user")
            ->setEmail("amdin@qqchose.fr")
            ->setPassword($this->encoder->encodePassword($user, "user"))
            ->setGender(1)
            ->setCreatedAt(new \DateTime())
            ->setRoles(["ROLE_USER"])
        ;

        $manager->persist($user);

        $project = new Project();
        $project->setName("Tablettes Ipad")
            ->setStatus(0)
            ->setCreatedAt(new \DateTime())
            ->setAuthor($user);

        $manager->persist($project);

        $project = new Project();
        $project->setName("SFR")
            ->setStatus(0)
            ->setCreatedAt(new \DateTime())
            ->setAuthor($user);

        $project = new Project();
        $project->setName("Réseaux")
            ->setStatus(0)
            ->setCreatedAt(new \DateTime())
            ->setAuthor($user);

        $manager->persist($project);

        $manager->flush();
    }
}
