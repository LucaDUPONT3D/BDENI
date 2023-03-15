<?php

namespace App\Security\Voter;

use App\Entity\Sortie;
use App\Entity\User;
use App\Repository\EtatRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class SortieVoter extends Voter
{
    public const SORTIE_UPDATE = 'sortie_update';

    public const SORTIE_DISPLAY = 'sortie_display';

    public const SORTIE_CANCEL = 'sortie_cancel';

    public const SORTIE_SUBSCRIBE = 'sortie_subscribe';

    public const SORTIE_UNSUBSCRIBE = 'sortie_unsubscribe';


    public function __construct(private EtatRepository $etatRepository)
    {
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute,
                [self::SORTIE_UPDATE, self::SORTIE_DISPLAY, self::SORTIE_CANCEL, self::SORTIE_SUBSCRIBE, self::SORTIE_UNSUBSCRIBE])
            && $subject instanceof \App\Entity\Sortie;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        if (!$user instanceof UserInterface) {
            return false;
        }

        if (null === $subject->getOrganisateur()){
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case self::SORTIE_UPDATE:
                return $this->canUpdateDeletePublish($subject, $user);
                break;
            case self::SORTIE_DISPLAY:
                return $this->canDisplay();
            case self::SORTIE_CANCEL:
                return $this->canCancel($subject, $user);
            case self::SORTIE_SUBSCRIBE:
                return $this->canSubscribe($subject, $user);
            case self::SORTIE_UNSUBSCRIBE:
                return $this->canUnsubscribe($subject, $user);
        }

        return false;
    }

    private function canUpdateDeletePublish(Sortie $sortie, User $user): bool
    {
        return $user === $sortie->getOrganisateur() && $sortie->getEtat() === $this->etatRepository->find(1);
    }

    private function canDisplay(): bool
    {
        return true;
    }

    private function canCancel(Sortie $sortie, User $user): bool
    {
        return ($user === $sortie->getOrganisateur() ||
            in_array('ROLE_ADMIN', $user->getRoles())) &&
            ($sortie->getEtat() === $this->etatRepository->find(2) ||
            $sortie->getEtat() === $this->etatRepository->find(3));
    }

    private function canSubscribe(Sortie $sortie, User $user): bool
    {
        return $user !== $sortie->getOrganisateur() &&
             !$sortie->getParticipants()->contains($user) &&
            $sortie->getEtat() === $this->etatRepository->find(2);
    }

    private function canUnsubscribe(Sortie $sortie, User $user): bool
    {
        return $user !== $sortie->getOrganisateur() &&
            $sortie->getParticipants()->contains($user) &&
            ($sortie->getEtat() === $this->etatRepository->find(2) ||
            $sortie->getEtat() === $this->etatRepository->find(3));
    }
}
