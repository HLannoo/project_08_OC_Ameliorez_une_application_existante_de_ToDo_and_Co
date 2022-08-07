<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use App\Tests\Fixtures\ToDoFixturesTest;


/**
 * @covers \App\Controller\SecurityController
 */
class SecurityControllerTest extends ToDoFixturesTest
{
    private $user;
    private $client;
    private $testUser;


    public function setUp(): void
    {
        $this->initializeTest();
        self::ensureKernelShutdown();
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

    protected function tearDown(): void
    {
        $this->tearDownTest();
    }

}
