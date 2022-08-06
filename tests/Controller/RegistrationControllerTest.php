<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RegistrationControllerTest extends WebTestCase
{
    private $client;
    private $userRepository;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);


    }
    public function testRegisterPageIsUp(): void
    {
        $this->client->request('GET', '/register');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals('/register',$this->client->getRequest()->getRequestUri() );

    }

        public function testRegisterProcessWhenIsValid(): void
    {
        $this->client->request('GET', '/register');
        $data = ["imtestform", "registerformtest@gmail.com", "registerpassword",1];
        $this->FormRegister($data);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertSelectorTextContains('',"Merci de vous connecter et de cliquer sur le lien d'activation envoyé par mail");
        $this->assertInstanceOf(User::class, $this->userRepository->findOneByEmail('registerformtest@gmail.com'));

    }

    public function FormRegister($data)
    {
        $this->client->request('GET', '/register');
        return $this->client->submitForm('Register', [
            'registration_form[username]' => $data[0],
            'registration_form[email]' => $data[1],
            'registration_form[password][first]' => $data[2],
            'registration_form[password][second]' => $data[2],
            'registration_form[agreeTerms]' => $data[3] ,
        ]);
    }
}
