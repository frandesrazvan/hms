<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HotelManagementController extends AbstractController
{
    public const HOTEL_INFORMATION_ROUTE_NAME = 'hotelInformationPage';
    public const RESERVATION_MANAGEMENT_ROUTE_NAME = 'reservationManagementPage';
    public const ROOM_MANAGEMENT_ROUTE_NAME = 'roomManagementPage';

    #[Route(path: '/hotel-information', name: self::HOTEL_INFORMATION_ROUTE_NAME)]
    public function launchHotelInformationPage(): Response
    {
        return $this->render('hotel-management/hotel-information.twig');
    }

    #[Route(path: '/reservation-management', name: self::RESERVATION_MANAGEMENT_ROUTE_NAME)]
    public function launchReservationManagementPage(): Response
    {
        return $this->render('hotel-management/reservation-management.twig');
    }

    #[Route(path: '/room-management', name: self::ROOM_MANAGEMENT_ROUTE_NAME)]
    public function launchRoomManagementPage(): Response
    {
        return $this->render('hotel-management/room-management.twig');
    }
}
