<?php

namespace App\Security\Voter;

use App\Repository\TaskRepository;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

class TaskVoter extends Voter
{
    public const EDIT = 'TASK_EDIT';
    public const DELETE = 'TASK_DELETE';
    public const TOGGLE = 'TASK_TOGGLE';

    function __construct(protected TaskRepository $taskRepository)
    {
        $this->taskRepository = $taskRepository;
    }
    protected function supports(string $attribute, $subject): bool
    {
        // replace with your own logic
        // https://symfony.com/doc/current/security/voters.html
        return in_array($attribute, ['TASK_EDIT','TASK_DELETE','TASK_TOGGLE'])
            && $subject instanceof \App\Entity\Task;
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
            case 'TASK_EDIT':
                return $user === $subject->getCurrentUser() && $user->getRoles()[0] === "ROLE_USER";
                break;
            case 'TASK_DELETE':
                    return $user === $subject->getCurrentUser() && $user->getRoles()[0] === "ROLE_USER";
                break;
            case 'TASK_TOGGLE':
                    return $user === $subject->getCurrentUser() && $user->getRoles()[0] === "ROLE_USER";
                break;
        }

        return false;
    }
}
