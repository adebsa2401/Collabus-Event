<?php

namespace App\Security\Voter;

use App\Entity\Company;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class CompanyVoter extends Voter
{
    public const CREATE = 'COMPANY_CREATE';
    public const EDIT = 'COMPANY_EDIT';
    public const VIEW = 'COMPANY_VIEW';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, [self::CREATE, self::EDIT, self::VIEW])
            && $subject instanceof \App\Entity\Company;
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
            self::VIEW => $this->canView($subject, $user),
            default => false,
        };
    }

    private function canCreate(Company $company, User $user)
    {
        if ($user->getCompanyProfile()) {
            return true;
        }
        return false;
    }

    private function canEdit(Company $company, User $user)
    {
        if ($company->getOwner() === $user->getCompanyProfile()) {
            return true;
        }
        return false;
    }

    private function canView(Company $company, User $user)
    {
        if ($this->security->isGranted('ROLE_ADMIN') || $company->getOwner() === $user->getCompanyProfile()) {
            return true;
        }
        return false;
    }
}
