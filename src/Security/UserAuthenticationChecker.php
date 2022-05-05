<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\User;
use Symfony\Component\Security\Core\Exception\AccountExpiredException;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAccountStatusException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserAuthenticationChecker implements UserCheckerInterface
{
    public function checkPreAuth(UserInterface $user): void
    {
    }

    public function checkPostAuth(UserInterface $user): void
    {
        if ($user->getDeletedAt() != null) {
            throw new CustomUserMessageAccountStatusException('Account does not exist or deleted.');
        }

        if ($user->getIsActive() === false) {
            throw new CustomUserMessageAccountStatusException('Please activate the account before login!');
        }
    }
}
