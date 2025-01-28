<?php

namespace App\DataFixtures;

use App\Entity\DoctorInfo;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{


    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        // Create a user with the ROLE_ADMIN role
        $admin = new User(
            'Laure',
            'Leleu',
            'admin@docontime.fr',
            ['ROLE_ADMIN'],
            '0989605267',
        );
        $admin->setPassword($this->passwordHasher->hashPassword($admin, 'password'));
        $admin->setAddress('7, place Fernandes');
        $admin->setZipCode(88179);
        $admin->setCity('Alves-la-Forêt');
        $manager->persist($admin);

        // Create a user with the ROLE_DOCTOR role
        $doctor = new User(
            'Gabriel',
            'Robert',
            'jacques.hardy@dupuis.fr',
            ['ROLE_DOCTOR'],
            '0331579444'
        );
        $doctor->setPassword($this->passwordHasher->hashPassword($doctor, 'password'));
        $doctor->setAddress('7, place Leduc');
        $doctor->setZipCode(35618);
        $doctor->setCity('Lopes-sur-Lelievre');
        $manager->persist($doctor);

        $doctorInfo = new DoctorInfo();
        $doctorInfo->setDoctor($doctor);
        $doctorInfo->setLocation('CHU de Lopes-sur-Lelievre');
        $doctorInfo->setSpeciality('Cardiologue');
        $manager->persist($doctorInfo);

        $manager->flush();
    }
}
