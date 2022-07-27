<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;


class UserControllerTest extends WebTestCase
{

    private $client;
    private $testAdmin;
    private $testUser;
    private $userRepository;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->testAdmin = $this->userRepository->findOneByEmail('bernard92@free.fr'); // Il s'agit d'un administrateur
        $this->testUser = $this->userRepository->findOneByEmail('astrid.barbe@royer.fr'); // Il s'agit d'un simple utilisateur

    }

    public function testUserPageRedirectWhenUserIsNotAdmin(): void
    {
        $this->client->loginUser($this->testUser);
        $this->client->request('GET', '/user');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testUserPageWhenUserIsAdmin(): void
    {
        $this->client->loginUser($this->testAdmin);
        $this->client->request('GET', '/user');

        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals('/user',$this->client->getRequest()->getRequestUri() );

    }

    public function testCreateUserPageRedirectWhenUserIsNotAdmin(): void
    {
        $this->client->loginUser($this->testUser);
        $this->client->request('GET', '/user/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }


    public function testCreateUserWhenUserIsAdmin(): void
    {
        $this->client->loginUser($this->testAdmin);
        $crawler = $this->client->request('GET', '/user/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $createButton = $crawler->selectButton("Ajouter");
        $form = $createButton->form();
        $form["user[email]"] = "user.test@gmail.com";
        $form["user[roles]"]->select('ROLE_USER');
        $form["user[password][first]"] = "password";
        $form["user[password][second]"] = "password";
        $form["user[username]"] = "User-Test";
        $this->client->submit($form);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"L'utilisateur a bien été ajouté");
        $this->assertInstanceOf(User::class, $this->userRepository->findOneByEmail('user.test@gmail.com'));
    }

    public function testEditUserPageRedirectWhenUserIsNotAdmin(): void
    {
        $this->client->loginUser($this->testUser);
        $userTest = $this->userRepository->findOneByEmail('user.test@gmail.com');
        $this->client->request('GET', '/user/'.$userTest->getId().'/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testEditUserWhenUserIsAdmin(): void
    {
        $this->client->loginUser($this->testAdmin);
        $userTest = $this->userRepository->findOneByEmail('user.test@gmail.com');
        $crawler = $this->client->request('GET', '/user/'.$userTest->getId().'/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Modifier')->form([
            'user[email]' => 'user.test.modify@gmail.com',
            'user[roles]'=> ('ROLE_ADMIN'),
            'user[username]' => 'userTestModify'
        ]);
        $this->client->submit($form);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"L'utilisateur a bien été modifié");
        $this->assertTrue( "user.test.modify@gmail.com" === $this->userRepository->find($userTest->getId())->getEmail());
    }

    public function testDeleteUserRedirectWhenUserIsNotAdmin(): void
    {
        $this->client->loginUser($this->testUser);
        $userTest = $this->userRepository->findOneByEmail('user.test.modify@gmail.com');
        $this->client->request('GET', '/user/'.$userTest->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testDeleteUserWhenUserIsAdmin(): void
    {
        $this->client->loginUser($this->testAdmin);
        $userTest = $this->userRepository->findOneByEmail('user.test.modify@gmail.com');
        $this->client->request('GET', '/user/'.$userTest->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"Le user {$userTest->getUsername()} a été supprimé avec succès !");
        $this->assertNull($this->userRepository->findOneByEmail($userTest));
    }


}