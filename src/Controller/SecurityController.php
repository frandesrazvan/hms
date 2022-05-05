<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    public const LOGIN_ROUTE_NAME = 'app_login';
    public const LOGOUT_ROUTE_NAME = 'app_logout';

    #[Route(path: '/login', name: self::LOGIN_ROUTE_NAME)]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('homepage');
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', ['lastUsername' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: self::LOGOUT_ROUTE_NAME)]
    public function logout(AuthenticationUtils $authenticationUtils): Response
    {
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('login/login.html.twig', ['lastUsername' => $lastUsername]);
    }
}
