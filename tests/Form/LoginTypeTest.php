<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\LoginType;
use Symfony\Component\Form\Test\TypeTestCase;

class LoginTypeTest extends TypeTestCase
{
    public function testLoginForm(): void
    {
        $data = ['email' => 'testEmail', 'password' => 'testEmail' ];

        $loginTest = New User();
        $form = $this->factory->create(LoginType::class, $loginTest);
        $form->submit($data);

        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());
        $this->assertInstanceOf(User::class, $form->getData());
    }
}
