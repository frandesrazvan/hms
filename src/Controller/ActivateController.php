<?php

declare(strict_types=1);

namespace App\Controller;

use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ActivateController extends AbstractController
{
    public const ACTIVATE_ROUTE_NAME = 'activatePage';

    #[Route(path: '/activate/{username}', name: self::ACTIVATE_ROUTE_NAME, requirements: ['username' => '.+'])]
    public function register(string $username, UserManager $userManager): Response
    {
        $user = $userManager->decryptToken($username);
        $userManager->activateUser($user);

        return $this->render('activate/activateAccount.html.twig', [
            'username' => $user,
        ]);
    }
}
