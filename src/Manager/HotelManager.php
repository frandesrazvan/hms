<?php

declare(strict_types=1);

namespace App\Manager;

use App\Service\HotelService;
use App\Entity\User;
use App\Entity\Hotel;
use App\Enum\UserEnum;

class HotelManager
{
    public function __construct(
        private HotelService $hotelService,
    ){
    }

    public function getHotelsByUser(User $user): array
    {
        $hotels = [];
        if ($user->isOwner()) {
            $hotels = $this->hotelService->getHotelsByOwner($user);
        }
        if ($user->isManager()) {
            $hotels[] = $user->getHotel();
        }

        return $hotels;
    }
}
