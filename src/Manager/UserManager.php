<?php

declare(strict_types=1);

namespace App\Manager;

use App\Entity\User;
use App\Service\ActivateService;
use App\Service\FileUploader;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UserManager
{
    public function __construct(
        private UserService $userService,
        private ActivateService $activateService,
        private FileUploader $fileUploader,
    ) {
    }

    public function registerUser(UploadedFile|null $file, User $user): void
    {
        $user->setProfilePicture(null);
        if ($file) {
            $photoFileName = $this->fileUploader->upload($file);
            $user->setProfilePicture($photoFileName);
        }
        $this->userService->register($user);
        $this->sendActivationMail($user);
    }

    public function removeUser(int $userId, User $loggedUser)
    {
        $this->userService->deleteUser($userId, $loggedUser);
    }

    public function sendActivationMail(User $user): void
    {
        $this->activateService->sendActivationMail($user);
    }

    public function getStaff(User $loggedUser, string $hotelParam, int $pageNumber): array
    {
        return $this->userService->getHotelStaff($loggedUser, $hotelParam, $pageNumber);
    }

    public function getNumberStaff(User $loggedUser, string $hotelParam): array
    {
        return $this->userService->getNumberStaff($loggedUser, $hotelParam);
    }

    public function decryptToken(string $token): string
    {
        return $this->activateService->decryptToken($token);
    }

    public function activateUser(string $username): bool
    {
        return $this->activateService->activateUser($username);
    }

    public function updateUser(UploadedFile|null $file, User $user): void
    {
        if ($file){
            $photoFileName = $this->fileUploader->upload($file);
            $user->setProfilePicture($photoFileName);
        }
        $this->userService->addUser($user);
    }

    public function deletePhoto($user): void
    {
        $this->userService->deletePhoto($user);
    }
}
