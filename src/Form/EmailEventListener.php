<?php

declare(strict_types=1);

namespace App\Form;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Event\PreSubmitEvent;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Security;

class EmailEventListener implements EventSubscriberInterface
{
    private Security $security;

    public function __construct(private EntityManagerInterface $entityManager, Security $security)
    {
        $this->security = $security;
    }

    #[ArrayShape([FormEvents::PRE_SUBMIT => "string"])] public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'onPreSubmit',
        ];
    }

    public function onPreSubmit(PreSubmitEvent $event): void
    {
        $form = $event->getForm();
        $email = $event->getData();
        $user = $this->security->getUser();
        $userDB = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
        if (($user == null && $userDB)||($userDB && $userDB->getUserIdentifier() != $user->getUserIdentifier())) {
            $form->addError(new FormError('This email is taken'));
        }
    }
}
