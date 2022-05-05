<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookingsController extends AbstractController
{
    public const BOOKINGS_ROUTE_NAME = 'bookings';

    #[Route(path: '/bookings', name: self::BOOKINGS_ROUTE_NAME)]
    public function launchBookingsPage(): Response
    {
        return $this->render('bookings/bookings.html.twig');
    }
}
