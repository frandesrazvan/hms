<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\Repository\HotelRepository;
use App\Entity\User;
use App\Entity\Hotel;

class HotelService
{
    public function __construct(
        private HotelRepository $hotelRepository,
    ){
    }

    public function getHotelsByOwner(User $user): array
    {
        return $this->hotelRepository->findBy([
            'owner' => $user->getId(),
        ]);
    }
}
