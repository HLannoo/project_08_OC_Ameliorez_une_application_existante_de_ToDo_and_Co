<?php

namespace App\Tests\Form;

use App\Entity\User;
use App\Form\ChangePasswordFormType;
use Symfony\Component\Form\Extension\Validator\Type\RepeatedTypeValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;


class ChangePasswordFormTypeTest extends TypeTestCase
{
    use \Symfony\Component\Form\Test\Traits\ValidatorExtensionTrait;

    public function testChangePassword(): void
    {

        $data = [
            'email' => 'test@gmail.com',
            'plainPassword' => array('first' => 'testPassword1', 'second' => 'testPassword1'),
        ];

        $form = $this->factory->create(ChangePasswordFormType::class, $data);
        $form->submit($data);



        $this->assertTrue($form->isSubmitted());
        $this->assertTrue($form->isValid());
        $this->assertEquals($data['plainPassword']['first'], $data['plainPassword']['second']);

    }

}
