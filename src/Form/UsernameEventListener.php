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

class UsernameEventListener implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
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
        $username = $event->getData();

        if ($this->entityManager->getRepository(User::class)->findOneBy(['username' => $username])) {
            $form->addError(new FormError('This username is taken'));
        }
    }
}
