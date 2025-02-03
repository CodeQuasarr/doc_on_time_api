<?php

namespace App\Application\Services;

use App\Entity\DoctorInfo;
use App\Repository\DoctorInfoRepository;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class BaseServices
{
    protected const ERROR_DOCTOR_NOT_FOUND = 'Doctor not found';
    protected DoctorInfoRepository $doctorInfoRepository;

    public function __construct(DoctorInfoRepository $doctorInfoRepository)
    {
        $this->doctorInfoRepository = $doctorInfoRepository;
    }

    /**
     * Retrieves valid doctor information for a given user.
     *
     * @param UserInterface $user The user object to find the associated doctor information.
     * @return DoctorInfo Returns the DoctorInfo object associated with the user.
     * @throws HttpException If the doctor information is not found, a 404 HTTP exception is thrown.
     */
    protected function getValidDoctorInfo(UserInterface $user): DoctorInfo
    {
        $doctorInfo = $this->doctorInfoRepository->findOneBy(['doctor' => $user->getId()]);

        if (!$doctorInfo) {
            throw new HttpException(Response::HTTP_NOT_FOUND, self::ERROR_DOCTOR_NOT_FOUND);
        }

        return $doctorInfo;
    }
}