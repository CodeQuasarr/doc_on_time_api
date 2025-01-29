<?php

namespace App\DataFixtures;

use App\Entity\Availability;
use App\Entity\DoctorInfo;
use App\Entity\User;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{


    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    /**
     * @throws \DateMalformedStringException
     */
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

        // Création des disponibilités des docteurs
        $availabilities = [
            [
                'doctor' => $doctorInfo,
                'date' => '2025-01-30',
                'slots' => ['09:00', '10:00', '14:00', '15:00', '17:00'],
            ],
            [
                'doctor' => $doctorInfo,
                'date' => '2025-02-03',
                'slots' => ['10:00', '11:00', '14:00', '15:00', '16:00', '17:00', '18:00'],
            ],
            [
                'doctor' => $doctorInfo,
                'date' => '2025-02-04',
                'slots' => ['10:00', '11:00', '16:00', '17:00', '18:00'],
            ],
        ];

        foreach ($availabilities as $availability) {
            $doctor = $availability['doctor'];
            // convertir date en dattimeinterface
            $date = new DateTimeImmutable($availability['date']);
            $slots = $availability['slots'];

            $doctorAvailability = new Availability($doctor, $date, $slots);
            $manager->persist($doctorAvailability);
        }

        $manager->flush();
    }
}
