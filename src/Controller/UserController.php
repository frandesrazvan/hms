<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\AddUserType;
use App\Form\RegisterType;
use App\Manager\HotelManager;
use App\Manager\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    public const REGISTER_ROUTE_NAME = 'registerPage';
    public const ADD_USER_ROUTE_NAME = 'addUserPage';


    #[Route(path: '/register', name: self::REGISTER_ROUTE_NAME)]
    public function launchRegisterPage(
        Request $request,
        UserManager $userManager,
    ): Response {
        $securityContext = $this->container->get('security.authorization_checker');
        if ($securityContext->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('homepage');
        }

        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('profilePicture')->getData();
            $user = $form->getData();

            $userManager->registerUser($photoFile, $user);

            return $this->render('register/successRegister.html.twig');
        }

        return $this->render('register/registerPage.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route(path: '/user-management/add-user', name: self::ADD_USER_ROUTE_NAME)]
    public function launchAddUserPage(
        Request $request,
        UserManager $userManager,
        HotelManager $hotelManager
    ): Response {
        $user = new User();
        $hotels = $hotelManager->getHotelsByUser($this->getUser());
        $form = $this->createForm(AddUserType::class, $user, [
            'hotels_list' => $hotels,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setProfilePicture(null);
            $userManager->registerUser(null, $user);

            $this->addFlash('success', 'User added successfully. Thank you!');
        }

        return $this->render('user-management/add-user.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
