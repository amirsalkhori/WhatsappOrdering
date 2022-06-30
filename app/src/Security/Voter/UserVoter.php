<?php
// api/src/Security/Voter/BookVoter.php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    private $security = null;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports($attribute, $subject): bool
    {
        $supportsAttribute = in_array($attribute, ['USER_READ', 'USER_UPDATE']);
        $supportsSubject = $subject instanceof User;

        return $supportsAttribute && $supportsSubject;
    }

    /**
     * @param string $attribute
     * @param User $subject
     * @param TokenInterface $token
     * @return bool
     */
    protected function voteOnAttribute($attribute, $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();

        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }
        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            return true;
        }
        switch ($attribute) {
            case 'USER_READ':
            case 'USER_UPDATE':
                if (in_array('ROLE_USER', $user->getRoles()) && $user->getId() == $subject->getId()) {
                    return true;
                }
                break;
        }

        return false;

    }
}