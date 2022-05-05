<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class ActivateService extends AbstractController
{
    public function __construct(
        private MailerInterface $mailer,
        private string $encryptionKey,
        private string $route,
        private string $sender,
        private string $encryptionAlgorithm = 'aes-128-ecb',
    ) {
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendActivationMail(User $user): void
    {
        $token = $this->encryptToken($user);
        $link =  $this->route . $token;
        $email = (new TemplatedEmail())
            ->from($this->sender)
            ->to($user->getEmail())
            ->subject('Thanks for signing up!')
            ->htmlTemplate('register/activateAccountMail.html.twig')
            ->context([
                'link' => $link,
                'username' => $user->getUserIdentifier(),
                'eemail' => $user->getEmail(),
                'dateOfBirth' => $user->getDateOfBirth()->format('Y-m-d'),
                'gender' => $user->getGender(),
                'firstName' => $user->getFirstName(),
                'lastName' => $user->getLastName(),
            ]);
        $this->mailer->send($email);
    }

    public function encryptToken(User $user): string
    {
        return openssl_encrypt($user->getUserIdentifier(), $this->encryptionAlgorithm, $this->encryptionKey);
    }

    public function decryptToken(string $token): string
    {
        return openssl_decrypt($token, $this->encryptionAlgorithm, $this->encryptionKey);
    }

    public function activateUser(string $username): bool
    {
        $entityManager = $this->getDoctrine()->getManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['username' => $username]);

        if (!$user) {
            return false;
        }

        $user->setIsActive(true);
        $entityManager->flush();

        return true;
    }
}
