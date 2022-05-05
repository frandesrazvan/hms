<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Enum\StaffEnum;
use App\Exceptions\UserNotAllowedToDeleteSelectedUserException;
use App\Repository\HotelRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use App\Enum\UserEnum;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;

class UserService extends AbstractController
{

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $encoder,
        private UserRepository $userRepository,
        private HotelRepository $hotelRepository,
    ) {
    }

    public function register(User $user): void
    {
        $user->setPassword(
            $this->encoder->hashPassword($user, $user->getPassword()),
        );

        $roles = [UserEnum::CLIENT_ROLE];

        if (!empty($user->getRole())) {
            $roles = [$user->getRole()];
        }

        if ($user->isManager() || $user->isEmployee()) {
            $user->setHotel($user->getHotel());
        }

        $user->setRoles($roles);

        $picture = $this->getPicture($user);

        if ($user->getProfilePicture() != null) {
            $picture = $user->getProfilePicture();
        }

        $user->setProfilePicture($picture);
        $user->setIsActive(false);
        $user->setRegistrationDate(new \DateTime());
        $this->addUser($user);
    }

    public function addUser(User $user): void
    {
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    public function deletePhoto($user): void
    {
        $user->setProfilePicture($this->getPicture($user));
        $this->addUser($user);
    }

    public function getPicture(User $user): string
    {
        return match ($user->getRoles()[0]) {
            UserEnum::ROLE . UserEnum::MANAGER_ROLE => UserEnum::MANAGER_PICTURE,
            UserEnum::ROLE . UserEnum::EMPLOYEE_ROLE => UserEnum::EMPLOYEE_PICTURE,
            UserEnum::ROLE . UserEnum::OWNER_ROLE => UserEnum::OWNER_PICTURE,
            default => UserEnum::CLIENT_PICTURE,
        };
    }

    public function getHotelStaff(User $loggedUser, string $hotelParam, int $pageNumber): array
    {
        if ($hotelParam !== StaffEnum::SHOW_ALL) {

            if ($loggedUser->isManager() && $hotelParam != $loggedUser->getHotel()->getId()) {
                return [];
            }

            return $this->entityManager->getRepository(User::class)->findStaff($hotelParam, $pageNumber);
        }

        return $loggedUser->isManager() ? $this->entityManager->getRepository(User::class)->findStaff($loggedUser->getHotel(),
            $pageNumber) : $this->entityManager->getRepository(User::class)->findOwnersStaff($loggedUser, $pageNumber);
    }

    public function getNumberStaff(User $loggedUser, string $hotelParam): array
    {
        if ($hotelParam !== StaffEnum::SHOW_ALL) {

            if ($loggedUser->isManager() && $hotelParam != $loggedUser->getHotel()->getId()) {
                return [];
            }

            return $this->entityManager->getRepository(User::class)->findNumberStaff($hotelParam);
        }

        return $loggedUser->isManager() ? $this->entityManager->getRepository(User::class)->findNumberStaff($loggedUser->getHotel()) : $this->entityManager->getRepository(User::class)->findOwnersNumberStaff($loggedUser);
    }

    /**
     * @throws UserNotAllowedToDeleteSelectedUserException
     */
    public function deleteUser(int $userId, User $loggedUser): void
    {
        $user = $this->userRepository->findOneBy([
            'id' => $userId,
        ]);

        if ($loggedUser->isManager() && $user->getHotel() !== $loggedUser->getHotel()) {
            throw new UserNotAllowedToDeleteSelectedUserException('The selected user cannot be deleted!');
        }

        if ($loggedUser->isOwner() && $user->getHotel()->getOwner()->getId() !== $loggedUser->getId()) {
            throw new UserNotAllowedToDeleteSelectedUserException('The selected user cannot be deleted!');
        }

        $user->setDeletedAt(new \DateTime());
        $this->addUser($user);

    }
}
