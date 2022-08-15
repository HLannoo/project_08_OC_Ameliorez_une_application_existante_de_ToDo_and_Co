<?php

namespace App\Tests\Security\Voter;

use App\Entity\Task;
use App\Entity\User;
use App\Security\Voter\TaskVoter;
use App\Security\Voter\UserVoter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class UserVoterTest extends TestCase
{

    private function createUser(int $id, $roles)
    {
        $user = $this->createMock(User::class);
        $user->method('getId')->willReturn($id);
        $user->method('getRoles')->willReturn($roles);

        return $user;
    }

    public function provideCases()
    {
        yield 'N\'est pas un User valide' => [
            'CAN_EDIT',
            null,
            $this->createUser(1, ['ROLE_ADMIN']),
            UserVoter::ACCESS_ABSTAIN,

        ];
        yield 'L\'utilisateur ne peut pas editer un user' => [
            'CAN_EDIT',
            $this->createUser(1, ['ROLE_USER']),
            $this->createUser(2, ['ROLE_USER']),
            UserVoter::ACCESS_DENIED,

        ];
        yield 'L\'administrateur peut editer un user' => [
            'CAN_EDIT',
            $this->createUser(1, ['ROLE_ADMIN']),
            $this->createUser(2, ['ROLE_USER']),
            UserVoter::ACCESS_GRANTED,

        ];
        yield 'L\'utilisateur ne peut pas supprimer un user' => [
            'CAN_DELETE',
            $this->createUser(1, ['ROLE_USER']),
            $this->createUser(2, ['ROLE_USER']),
            UserVoter::ACCESS_DENIED,

        ];
        yield 'L\'administrateur peut supprimer un user' => [
            'CAN_DELETE',
            $this->createUser(1, ['ROLE_ADMIN']),
            $this->createUser(2, ['ROLE_USER']),
            UserVoter::ACCESS_GRANTED,

        ];
        yield 'L\'utilisateur ne peut pas créer un user' => [
            'CAN_CREATE',
            $this->createUser(1, ['ROLE_USER']),
            $this->createUser(2, ['ROLE_USER']),
            UserVoter::ACCESS_DENIED,

        ];
        yield 'L\'administrateur peut créer un user' => [
            'CAN_CREATE',
            $this->createUser(1, ['ROLE_ADMIN']),
            $this->createUser(2, ['ROLE_USER']),
            UserVoter::ACCESS_GRANTED,

        ];
        yield 'L\'utilisateur ne peut pas regarder la liste des users' => [
            'CAN_VIEW',
            $this->createUser(1, ['ROLE_USER']),
            $this->createUser(2, ['ROLE_USER']),
            UserVoter::ACCESS_DENIED,

        ];
        yield 'L\'administrateur peut regarder la liste des users' => [
            'CAN_VIEW',
            $this->createUser(1, ['ROLE_ADMIN']),
            $this->createUser(2, ['ROLE_USER']),
            UserVoter::ACCESS_GRANTED,

        ];

    }

    /**
     * @dataProvider provideCases
     */
    public function testVote(string $attribute, $subject, $user, $expectedVote)
    {

        $voter = new UserVoter();

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
