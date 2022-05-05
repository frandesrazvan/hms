<?php

declare(strict_types=1);

namespace App\Controller;

use App\Enum\StaffEnum;
use App\Exceptions\AppExceptions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\UserManagementType;
use App\Manager\HotelManager;
use App\Manager\UserManager;

class UserManagementController extends AbstractController
{
    public const USER_MANAGEMENT_ROUTE_NAME = 'userManagementPage';
    public const DELETE_USER_ROUTE_NAME = 'deleteUserPage';

    #[Route(path: '/user-management/{hotelParam}/{pageNumber}', name: self::USER_MANAGEMENT_ROUTE_NAME, requirements: ['hotelParam' => StaffEnum::SHOW_ALL . '|\d+'], defaults: ['hotelParam' => StaffEnum::SHOW_ALL, 'pageNumber' => 1])]
    public function launchUserManagementPage(Request $request, HotelManager $hotelManager, UserManager $userManager, $hotelParam, $pageNumber): Response
    {
        $hotels = $hotelManager->getHotelsByUser($this->getUser());
        $usersArray = $userManager->getStaff($this->getUser(), $hotelParam, intval($pageNumber));
        $items = $userManager->getNumberStaff($this->getUser(), $hotelParam);

        return $this->render('user-management/user-management.html.twig', [
            'hotels_list' => $hotels,
            'hotel_staff' => $usersArray,
            'items' => $items[0][1],
            'userManagement' => $this->getParameter('userManagement'),
            'userManagementDelete' => $this->getParameter('userManagementDelete'),
        ]);
    }

    #[Route(path: '/user-management/delete/{userId}', name: self::DELETE_USER_ROUTE_NAME, requirements: ['userId' => '\d+'], methods: ['DELETE'])]
    public function deleteUser(int $userId, UserManager $userManager, HotelManager $hotelManager): Response
    {
        try {
            $userManager->removeUser($userId, $this->getUser());
        } catch (AppExceptions $e) {
            $e->getMessage();
        }
        $hotels = $hotelManager->getHotelsByUser($this->getUser());

        $usersArray = $userManager->getStaff($this->getUser(), StaffEnum::SHOW_ALL);

        return $this->render('user-management/user-management.html.twig', [
            'hotels_list' => $hotels,
            'hotel_staff' => $usersArray,
            'userManagement' => $this->getParameter('userManagement'),
            'userManagementDelete' => $this->getParameter('userManagementDelete'),
        ]);
    }
}
