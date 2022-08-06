<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Component\Form\Test\TypeTestCase;

class UserTypeTest extends TypeTestCase
{
    public function testUserForm(): void
    {
        $data = [
            'email' => 'testemail@gmail.com',
            'roles' => "ROLE_USER",
            'password' => array('first' => 'testPassword', 'second' => 'testPassword'),
            'username' => 'testName'
        ];


        $userTest = new User;
        $form = $this->factory->create(UserType::class, $userTest);
        $form->submit($data);

        $user = new User;
        $user->setEmail('testemail@gmail.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword('testPassword');
        $user->setUsername('testName');



        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());
        $this->assertEquals($user->getEmail(), $userTest->getEmail());
        $this->assertEquals($user->getPassword(), $userTest->getPassword());
        $this->assertEquals($user->getRoles(), $userTest->getRoles());
        $this->assertEquals($user->getUsername(), $userTest->getUsername());
        $this->assertInstanceOf(User::class, $form->getData());
    }
}
