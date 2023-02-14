<?php

namespace App\Security\Voter;

use App\Entity\ActivityArea;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class ActivityAreaVoter extends Voter
{
    public const CREATE = 'ACTIVITY_AREA_CREATE';
    public const EDIT = 'ACTIVITY_AREA_EDIT';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return (in_array($attribute, [self::EDIT])
            && $subject instanceof \App\Entity\ActivityArea)
            || (in_array($attribute, [self::CREATE]));
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        return match($attribute) {
            self::CREATE => $this->canCreate($subject, $user),
            self::EDIT => $this->canEdit($subject, $user),
            default => false,
        };
    }

    private function canCreate(ActivityArea $activityArea, User $user)
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }

    private function canEdit(ActivityArea $activityArea, User $user)
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
