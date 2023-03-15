<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const USER_UPDATE = 'user_update';

    protected function supports(string $attribute, mixed $subject): bool
    {

        return in_array($attribute, [self::USER_UPDATE])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        switch ($attribute) {
            case self::USER_UPDATE:
                return $this->canUpdate($user, $subject);
        }

        return false;
    }

    private function canUpdate(User $user, User $subject): bool
    {
        return $user === $subject;
    }
}
