<?php

namespace App\DataFixtures;

use App\Entity\Appointment;
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
                'slots' => ['10:00', '11:00', '15:00', '16:00', '17:00', '18:00'],
            ],
        ];

        foreach ($availabilities as $availability) {
            $doctorAvailability = new Availability($availability['doctor'], $availability['date'], $availability['slots']);
            $manager->persist($doctorAvailability);
        }

        // Création des rendez-vous des patients
        $patients = [
            [
                'firstName' => 'Marcel',
                'lastName' => 'Marchand',
                'email' => 'valerie55@renault.fr',
                'phone' => '0585652991',
                'appointment' => [
                    [
                        'date' => '2025-02-03',
                        'time' => '18:00',
                        'status' => 'Programmée',
                        'type' => 'Consultation',
                    ],
                    [
                        'date' => '2025-02-04',
                        'time' => '18:00',
                        'status' => 'Programmée',
                        'type' => 'Consultation',
                    ],
                ],
            ],
            [
                'firstName' => 'Marc',
                'lastName' => 'Maillard',
                'email' => 'leblanc.amelie@legoff.fr',
                'phone' => '0186553222',
                'appointment' => [
                    [
                        'date' => '2025-02-03',
                        'time' => '14:00',
                        'status' => 'Programmée',
                        'type' => 'Consultation',
                    ],
                    [
                        'date' => '2025-02-04',
                        'time' => '17:00',
                        'status' => 'Programmée',
                        'type' => 'Consultation',
                    ],
                ],
            ],
            [
                'firstName' => 'Marie',
                'lastName' => 'Laine',
                'email' => 'hugues25@orange.fr',
                'phone' => '0158588481',
                'appointment' => [],
            ],
            [
                'firstName' => 'Jeannine',
                'lastName' => 'Lelievre',
                'email' => 'benjamin75@bouvet.fr',
                'phone' => '0114390566',
                'appointment' => [
                    [
                        'date' => '2025-02-03',
                        'time' => '11:00',
                        'status' => 'Programmée',
                        'type' => 'Consultation',
                    ],
                    [
                        'date' => '2025-02-04',
                        'time' => '15:00',
                        'status' => 'Programmée',
                        'type' => 'Examination',
                    ],
                ],
            ],
            [
                'firstName' => 'Christelle',
                'lastName' => 'Philippe',
                'email' => 'jules27@diaz.fr',
                'phone' => '0913437155',
                'appointment' => [
                    [
                        'date' => '2025-02-03',
                        'time' => '10:00',
                        'status' => 'Programmée',
                        'type' => 'Consultation',
                    ],
                    [
                        'date' => '2025-02-04',
                        'time' => '16:00',
                        'status' => 'En attente',
                        'type' => 'Examination',
                    ],
                ],
            ],
        ];

        $statuses = ['Programmée', 'Completed', 'Cancelled'];
        $types = ['Consultation', 'Follow-up', 'Urgency'];
        foreach ($patients as $patient) {
            $user = new User(
                first_name: $patient['firstName'],
                last_name: $patient['lastName'],
                email: $patient['email'],
                phone: $patient['phone']
            );
            $user->setPassword($this->passwordHasher->hashPassword($user, 'password'));
            $manager->persist($user);

            foreach ($patient['appointment'] as $appointment) {
                $appointment = new Appointment(
                    $doctor,
                    $user,
                    $appointment['date'],
                    $appointment['time'],
                    $appointment['status'],
                    $appointment['type']
                );
                $manager->persist($appointment);
            }
        }

        $manager->flush();
    }
}
