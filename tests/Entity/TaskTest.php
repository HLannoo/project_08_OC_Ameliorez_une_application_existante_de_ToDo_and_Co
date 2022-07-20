<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testGetId()
    {
        try {
            $task = new Task();
            $this->assertEquals(null, $task->getId());
        }
        catch(\Exception $exception){
            $this->assertStringContainsString('Le champ est différent de null', $exception->getMessage());
        }
    }

    public function testDateTimeImmutable()
    {
        try {
            $task = new Task();
            $task->setCreatedAt(new \DateTimeImmutable('12/12/12'));
            $this->assertEquals(new \DateTimeImmutable('12/12/12'), $task->getCreatedAt());
        }
        catch(\Exception $exception) {
            $this->assertStringContainsString("La date settée est différente de celle récupérée par le getter", $exception->getMessage());
        }
    }

    public function testTitle()
    {
        try {
            $task = new Task();
            $task->setTitle('testTitle');
            $this->assertEquals('testTitle', $task->getTitle());
        }
        catch(\Exception $exception) {
            $this->assertStringContainsString("Le titre setté est différent de celui récupéré par le getter", $exception->getMessage());
        }
    }

    public function testContent()
    {
        try {
            $task = new Task();
            $task->setContent('testContent');
            $this->assertEquals('testContent', $task->getContent());
        }
        catch(\Exception $exception) {
            $this->assertStringContainsString("Le titre setté est différent de celui récupéré par le getter", $exception->getMessage());
        }
    }

    public function testIsDone()
    {
        try {
            $task = new Task();
            $task->setIsDone(true);
            $this->assertSame(true, $task->isIsDone());
        }
        catch(\Exception $exception) {
            $this->assertStringContainsString("La valeur reçue par le getter est différente de celle envoyée par le setter", $exception->getMessage());
        }
    }
    public function testCurrentUser()
    {
        try {
            $user = new User;
            $task = new Task;
            $task->setCurrentUser($user);
            $this->assertNotNull($task->getCurrentUser());
        }
        catch(\Exception $exception){
            $this->assertStringContainsString("Aucun user n'a été trouvé pour cette tâche", $exception->getMessage());
        }

    }
}
