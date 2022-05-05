<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomePageController extends AbstractController
{
    public const HOME_ROUTE_NAME = 'homepage';

    #[Route(path: '/home', name: self::HOME_ROUTE_NAME)]
    public function launchHomepage(AuthenticationUtils $authenticationUtils): Response
    {
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('homepage/homepage.html.twig', ['lastUsername' => $lastUsername]);
    }
}
