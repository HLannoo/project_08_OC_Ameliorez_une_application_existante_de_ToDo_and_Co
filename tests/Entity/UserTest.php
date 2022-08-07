<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Entity\User
 */
class UserTest extends TestCase
{
    private $user;
    private $task;
    public function setUp(): void
    {
        $this->user = new User;
        $this->user->setEmail('test@gmail.com')
                ->setUsername('test@gmail.com')
                ->setPassword('test@gmail.com')
                ->setRoles(['ROLE_USER'])
                ->setIsVerified(1)
                ->setToken('testToken');
        $this->task = New Task;
    }
    public function testGetIsTrue(): void
    {

        $this->assertTrue('test@gmail.com' === $this->user->getEmail());
        $this->assertTrue('test@gmail.com' === $this->user->getUsername());
        $this->assertTrue('test@gmail.com' === $this->user->getPassword());
        $this->assertTrue( ['ROLE_USER'] === $this->user->getRoles());
        $this->assertTrue(true === $this->user->isVerified() );
        $this->assertTrue('testToken' === $this->user->getToken());
    }

    public function testGetIsFalse(): void
    {
        $this->assertFalse('false@gmail.com' === $this->user->getEmail());
        $this->assertFalse('falseusername' === $this->user->getUsername());
        $this->assertFalse('falsepassword' === $this->user->getPassword());
        $this->assertFalse(['ROLE_ADMIN'] === $this->user->getRoles());
        $this->assertFalse(false === $this->user->isVerified());
        $this->assertFalse('falseToken' === $this->user->getToken());
    }

    public function testGetIsEmpty(): void
    {
        $user = new User;

        $this->assertEmpty($user->getId());
        $this->assertEmpty($user->getEmail());
        $this->assertEmpty($user->getUsername());
        $this->assertEmpty($user->getUserIdentifier());
        $this->assertEmpty($user->getTask());
        $this->assertEmpty($user->isVerified());
        $this->assertEmpty($user->getToken());
        $this->assertEmpty($user->getRoles());
    }

    public function testTaskAdRemove(): void
    {
        $this->user->addTask($this->task);
        $this->assertContains($this->task, $this->user->getTask());

        $this->user->removeTask($this->task);
        $this->assertEmpty($this->user->getTask());
    }

}
