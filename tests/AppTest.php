<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class AppTest extends TestCase
{
    public function testGetId()
    {
        $user = New User;
        $this->assertEquals(null, $user->getId());
    }

    public function testSetGetEmail()
    {
        $user = New User;
        $user->setEmail('test@gmail.com');
        $this->assertEquals('test@gmail.com', $user->getEmail());
    }

    public function testSetGetUsername()
    {
        try {
            $user = new User;
            $user->setUsername('testu');
            $this->assertEquals('test', $user->getUsername());
        }
        catch(\Exception $exception){
            $this->assertStringContainsString('Le Username setter est différent de celui récupérer par le getter', $exception->getMessage());
        }
    }

    public function testGetUserIdentifier()
    {
        $user = New User;
        $user->setEmail('test@gmail.com');
        $this->assertEquals('test@gmail.com',$user->getUserIdentifier());
    }

    public function testGetSetGetRoles()
    {
        $user = New User;
        $user->setRoles(["ROLE_USER"]);
        $this->assertEquals(["ROLE_USER"], $user->getRoles());
    }

}
