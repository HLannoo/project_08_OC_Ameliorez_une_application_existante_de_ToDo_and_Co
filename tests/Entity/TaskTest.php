<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * @covers \App\Entity\Task
 */
class TaskTest extends TestCase
{
    private $user;
    private $task;
    public function setUp(): void
    {
        $this->user = New User();
        $this->task = new Task();
        $this->date = new \DateTimeImmutable();
        $this->task->setCurrentUser($this->user)
            ->setTitle('testTitle')
            ->setContent('testContent')
            ->setIsDone(1)
            ->setCreatedAt($this->date);

    }
    public function testIsTrue(): void
    {

        $this->assertTrue($this->user  === $this->task->getCurrentUser());
        $this->assertTrue('testTitle' === $this->task->getTitle());
        $this->assertTrue('testContent' === $this->task->getContent());
        $this->assertTrue( true === $this->task->isIsDone());
        $this->assertTrue($this->date === $this->task->getCreatedAt() );
    }

    public function testIsFalse(): void
    {
        $this->assertFalse( new User === $this->task->getCurrentUser());
        $this->assertFalse('falseTitle' === $this->task->getTitle());
        $this->assertFalse('falseContent' === $this->task->getContent());
        $this->assertFalse(false === $this->task->isIsDone());
        $this->assertFalse(new \DateTimeImmutable() ===  $this->task->getCreatedAt() );
    }

    public function testIsEmpty(): void
    {
        $task = new Task;

        $this->assertEmpty($task->getId());
        $this->assertEmpty($task->getTitle());
        $this->assertEmpty($task->getContent());
        $this->assertEmpty($task->getCreatedAt());
        $this->assertEmpty($task->getCurrentUser());
        $this->assertEmpty($task->isIsDone());

    }

}
