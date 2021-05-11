<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Faker;

class UserFixtures extends Fixture
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }


    public function load(ObjectManager $manager)
    {
        // Création de 5 utilisateurs classiques
        for ($i=0; $i < 5; $i++) {
            $faker = Faker\Factory::create('fr_FR');
            $user = new User();
            $user->setFirstname($faker->firstName);
            $user->setLastname($faker->lastname);
            $user->setCityAgency($faker->city);
            $user->setEmail('user'.$i.'@wiki.com');
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($this->passwordEncoder->encodePassword(
                $user,
                'userpassword'
            ));
            $user->setValidated(true);
            $manager->persist($user);
            $this->addReference('user_'. $i, $user);
        }

        // Création d’un utilisateur de type “modérateur”
        $moderator = new User();
        $moderator->setFirstname('Wild');
        $moderator->setLastname('Moderator');
        $moderator->setCityAgency($faker->city);
        $moderator->setEmail('moderator@wiki.com');
        $moderator->setRoles(['ROLE_MODERATOR']);
        $moderator->setPassword($this->passwordEncoder->encodePassword(
            $moderator,
            'moderatorpassword'
        ));
        $moderator->setValidated(true);

        $manager->persist($moderator);

        // Création d’un utilisateur de type “administrateur”
        $admin = new User();
        $admin->setFirstname('SuperWild');
        $admin->setLastname('Admin');
        $admin->setCityAgency($faker->city);
        $admin->setEmail('admin@wiki.com');
        $admin->setRoles(['ROLE_ADMIN']);
        $admin->setPassword($this->passwordEncoder->encodePassword(
            $admin,
            'adminpassword'
        ));
        $admin->setValidated(true);

        $manager->persist($admin);

        // Sauvegarde des nouveaux utilisateurs :

        $manager->flush();
    }
}
