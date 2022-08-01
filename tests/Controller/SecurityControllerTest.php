<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private $user;
    private $client;
    private $testUser;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $userRepository = static::getContainer()->get(UserRepository::class);
        $this->testUser = $userRepository->findOneByEmail('user-test@gmail.com');

    }

    public function testConnexionWithBadCredentials(): void
    {
        $this->client->request('GET', '/login');
        $this->client->submitForm('Connexion',[
            'login[email]' => 'false@Gmail.com',
            'login[password]' => 'falsePassword'
        ]);
        $this->assertSelectorTextContains('', 'Bad credentials.');

    }

    public function testConnexionWithGoodCredentials(): void
    {

        $this->client->request('GET', '/login');
        $this->client->submitForm('Connexion',[
            'login[email]' => 'user-test@gmail.com',
            'login[password]' => 'password'
        ]);
        $this->client->followRedirect();
        $this->assertEquals('/task',$this->client->getRequest()->getRequestUri());

    }


    public function testDisconnectButtonWhenUserIsLogged(): void
    {
        $this->client->loginUser($this->testUser);
        $this->client->request('GET', '/task');
        $this->client->clickLink('Déconnexion');
        $this->client->followRedirect();
        $this->assertEquals('/login',$this->client->getRequest()->getRequestUri());

    }


}
