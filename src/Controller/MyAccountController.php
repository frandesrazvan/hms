<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\User;
use App\Form\MyAccountType;
use App\Manager\UserManager;
use App\Service\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MyAccountController extends AbstractController
{
    public const MY_ACCOUNT_ROUTE_NAME = 'myAccountPage';
    public const DELETE_PHOTO_ROUTE_NAME = 'deletePhotoPage';

    #[Route(path: '/my-account', name: self::MY_ACCOUNT_ROUTE_NAME)]
    public function launchMyAccountPage(UserManager $userManager, Request $request): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $form = $this->createForm(MyAccountType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $photoFile = $form->get('profilePicture')->getData();
            $user = $form->getData();
            $userManager->updateUser($photoFile, $user);
        }

        return $this->render('my-account/my-account.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);
    }

    #[Route(path: '/my-account/delete-my-image', name: self::DELETE_PHOTO_ROUTE_NAME)]
    public function deleteUserPhoto(UserManager $userManager, Request $request, FileUploader $fileUploader): Response
    {
        $user = $this->getUser();
        $userManager->deletePhoto($user);

        return $this->launchMyAccountPage($userManager, $request, $fileUploader);
    }
}
