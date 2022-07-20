<?php

namespace App\Tests;

use App\Entity\Task;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testGetId()
    {
        try {
            $user = new User;
            $this->assertEquals(null, $user->getId());
        }
        catch(\Exception $exception){
            $this->assertStringContainsString('Le champ est différent de null', $exception->getMessage());
        }
    }

    public function testEmail()
    {
        try {
            $user = new User;
            $user->setEmail('test@gmail.com');
            $this->assertEquals('test@gmail.com', $user->getEmail());
        }
        catch(\Exception $exception) {
            $this->assertStringContainsString("L'email' setté est différent de celui récupéré par le getter", $exception->getMessage());
        }
    }

    public function testUsername()
    {
        try {
            $user = new User;
            $user->setUsername('test');
            $this->assertEquals('test', $user->getUsername());
        }
        catch(\Exception $exception){
            $this->assertStringContainsString('Le Username setté est différent de celui récupéré par le getter', $exception->getMessage());
        }
    }

    public function testGetUserIdentifier()
    {
        try {
        $user = New User;
        $user->setEmail('test@gmail.com');
        $this->assertEquals('test@gmail.com',$user->getUserIdentifier());
        }
        catch(\Exception $exception){
            $this->assertStringContainsString('Le UserIdentifier setté est différent de celui récupéré par le getter', $exception->getMessage());
        }
    }

    public function testRoles()
    {
        try {
        $user = New User;
        $user->setRoles(["ROLE_USER"]);
        $this->assertEquals(["ROLE_USER"], $user->getRoles());
        }
        catch(\Exception $exception){
            $this->assertStringContainsString('Le Roles setté est différent de celui récupéré par le getter', $exception->getMessage());
        }

    }

    public function testPassword()
    {
        try {
            $user = New User;
            $user->setPassword('testpassword');
            $this->assertEquals('testpassword', $user->getPassword());
        }
        catch(\Exception $exception){
            $this->assertStringContainsString('Le Password setté est différent de celui récupéré par le getter', $exception->getMessage());
        }

    }

    public function testEraseCredentials()
    {
        try {
            $user = New User;
            $this->assertNull($user->eraseCredentials());
        }
        catch(\Exception $exception){
            $this->assertStringContainsString("Le password n'a pas été correctement effacé", $exception->getMessage());
        }

    }
    public function testTask()
    {
        try {
            $user = new User;
            $task = new Task;
            $user->addTask($task);
            $this->assertCount(1, $user->getTask());
        }
        catch(\Exception $exception){
            $this->assertStringContainsString("La nombre de tâche est différent du nombre de tache réellement ajoutée", $exception->getMessage());
        }

        try {

            $tasks = $user->getTask();
            $this->assertSame($user->getTask(), $tasks);
        }
        catch(\Exception $exception){
            $this->assertStringContainsString("La tâche n'a pas pu être récupérée", $exception->getMessage());
        }

        try {
            $user->removeTask($task);
            $this->assertCount(0, $user->getTask());
        }
        catch(\Exception $exception) {
            $this->assertStringContainsString("La tâche n'a pas pu être éffacée", $exception->getMessage());
        }

    }

}
