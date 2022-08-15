<?php

namespace App\Tests\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use App\Security\Voter\TaskVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class TaskVoterTest extends TestCase
{

    private function createUser(int $id, $roles)
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($id);
        $user->method('getRoles')->willReturn($roles);

        return $user;
    }

    private function createTaskByUser($user = null)
    {
        $task = $this->createMock(Task::class);
        $task->method('getCurrentUser')->willReturn($user);

        return $task;
    }

    public function provideCases()
    {
        yield 'N\'est pas une Task valide' => [
            'TASK_EDIT',
            null,
            $this->createUser(1,['ROLE_USER']),
            TaskVoter::ACCESS_ABSTAIN,

        ];
        yield 'L\'utilisateur peut SUPPRIMER sa propre tâche' => [
            'TASK_DELETE',
            $this->createTaskByUser($user = $this->createUser(1,['ROLE_USER'])),
            $user,
            TaskVoter::ACCESS_GRANTED,
        ];
        yield 'L\'utilisateur peut EDITER sa propre tâche' => [
            'TASK_EDIT',
            $this->createTaskByUser($user = $this->createUser(1,['ROLE_USER'])),
            $user,
            TaskVoter::ACCESS_GRANTED,
        ];
        yield 'L\'utilisateur peut TOGGLE sa propre tâche' => [
            'TASK_TOGGLE',
            $this->createTaskByUser($user = $this->createUser(1,['ROLE_USER'])),
            $user,
            TaskVoter::ACCESS_GRANTED,
        ];
        yield 'L\'utilisateur ne peut pas SUPPRIMER les qu\'il n\'a pas créé' => [
            'TASK_DELETE',
            new Task(),
            $this->createUser(1,['ROLE_USER']),
            TaskVoter::ACCESS_DENIED
        ];
        yield 'L\'utilisateur ne peut pas EDITER les qu\'il n\'a pas créé' => [
            'TASK_EDIT',
            new Task(),
            $this->createUser(1,['ROLE_USER']),
            TaskVoter::ACCESS_DENIED
        ];
        yield 'L\'utilisateur ne peut pas TOGGLE les qu\'il n\'a pas créé' => [
            'TASK_TOGGLE',
            new Task(),
            $this->createUser(1,['ROLE_USER']),
            TaskVoter::ACCESS_DENIED
        ];


    }

    /**
     * @dataProvider provideCases
     */
    public function testVote(string $attribute, $subject, $user, $expectedVote)
    {

        $voter = new TaskVoter();

        if ($user) {
            $token = new UsernamePasswordToken(
                $user, 'credentials',
            );
        }

        $this->assertSame(
            $expectedVote,
            $voter->vote($token ,$subject, [$attribute])
        );
    }


}
