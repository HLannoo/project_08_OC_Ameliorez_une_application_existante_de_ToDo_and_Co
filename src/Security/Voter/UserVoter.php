<?php

namespace App\Security\Voter;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class UserVoter extends Voter
{
    public const EDIT = 'CAN_EDIT';
    public const VIEW = 'CAN_VIEW';
    public const CREATE = 'CAN_CREATE';
    public const DELETE = 'CAN_DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['CAN_EDIT','CAN_VIEW','CAN_CREATE','CAN_DELETE'])
            && $subject instanceof \App\Entity\User;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {

        $user = $token->getUser();
        // if the user is anonymous, do not grant access
        if (!$user instanceof UserInterface) {
            return false;
        }

        // ... (check conditions and return true to grant permission) ...
        switch ($attribute) {
            case 'CAN_EDIT':
                return $subject->getRoles()[0] == "ROLE_ADMIN";
                break;
            case 'CAN_VIEW':
                return $subject->getRoles()[0] == "ROLE_ADMIN";
                break;
            case 'CAN_CREATE':
                return $subject->getRoles()[0] == "ROLE_ADMIN";
                break;
            case 'CAN_DELETE':
                return $subject->getRoles()[0] == "ROLE_ADMIN";
                break;
        }

        return false;
    }
}
