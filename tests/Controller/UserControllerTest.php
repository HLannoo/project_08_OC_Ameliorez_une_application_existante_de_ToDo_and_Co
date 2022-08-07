<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Tests\Fixtures\ToDoFixturesTest;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @covers \App\Controller\UserController
 */
class UserControllerTest extends ToDoFixturesTest
{

    private $client;
    private $testAdmin;
    private $testUser;
    private $userRepository;


    public function setUp(): void
    {

        $this->initializeTest();
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->testAdmin = $this->userRepository->findOneByEmail('admin-test@gmail.com'); // Il s'agit d'un administrateur
        $this->testUser = $this->userRepository->findOneByEmail('user-test@gmail.com'); // Il s'agit d'un simple utilisateur

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
        $user = $this->testAdmin;
        $datas = ["user.test@gmail.com","ROLE_USER","password","password","User-Test"];
        $this->createForm($user,$datas);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"L'utilisateur a bien été ajouté");
        $this->assertInstanceOf(User::class, $this->userRepository->findOneByEmail('user.test@gmail.com'));
    }


    public function testEditUserPageRedirectWhenUserIsNotAdmin(): void
    {

        $user = $this->testAdmin;
        $datas = ["user.test+2@gmail.com","ROLE_USER","password","password","User-Test"];
        $this->createForm($user,$datas);
        $this->client->request('GET', '/logout');

        $this->client->loginUser($this->testUser);
        $userTest = $this->userRepository->findOneByEmail('user.test+2@gmail.com');
        $this->client->request('GET', '/user/'.$userTest->getId().'/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }


    public function testEditUserWhenUserIsAdmin(): void
    {
        $user = $this->testAdmin;
        $datas = ["user.test+3@gmail.com","ROLE_USER","password","password","User-Test"];
        $this->createForm($user,$datas);

        $userTest = $this->userRepository->findOneByEmail('user.test+3@gmail.com');
        $data = ["user.test+4@gmail.com","ROLE_USER","User-Test"];
        $this->EditForm($data,$user,$userTest);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);



        $this->assertSelectorTextContains('',"L'utilisateur a bien été modifié");
        $this->assertInstanceOf(User::class, $this->userRepository->findOneByEmail('user.test+4@gmail.com'));
    }


    public function testDeleteUserRedirectWhenUserIsNotAdmin(): void
    {
        $user = $this->testAdmin;
        $data = ["user.test+5@gmail.com","ROLE_USER","password","password","User-Test"];
        $this->createForm($user,$data);
        $this->client->request('GET', '/logout');

        $this->client->loginUser($this->testUser);
        $userTest = $this->userRepository->findOneByEmail('user.test+5@gmail.com');
        $this->client->request('GET', '/user/'.$userTest->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }


    public function testDeleteUserWhenUserIsAdmin(): void
    {
        $user = $this->testAdmin;
        $datas = ["user.test+6@gmail.com","ROLE_USER","password","password","User-Test"];
        $this->createForm($user,$datas);

        $userTest = $this->userRepository->findOneByEmail('user.test+6@gmail.com');
        $this->client->request('GET', '/user/'.$userTest->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"Le user {$userTest->getUsername()} a été supprimé avec succès !");
        $this->assertNull($this->userRepository->findOneByEmail($userTest));
    }

    private function createForm($user, $datas)
    {
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', '/user/create');
        $createButton = $crawler->selectButton("Ajouter");
        $form = $createButton->form();
        $form["user[email]"] = $datas[0];
        $form["user[roles]"]->select($datas[1]);
        $form["user[password][first]"] = $datas[2];
        $form["user[password][second]"] = $datas[3];
        $form["user[username]"] = $datas[4];

        return $this->client->submit($form);

    }

    private function EditForm($data, $user, $userTest)
    {
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', '/user/'.$userTest->getId().'/edit');
        $form = $crawler->selectButton("Modifier")->form([
            'user[email]' => $data[0],
            'user[roles]'=> $data[1],
            'user[username]'=> $data[2],
        ]);
        return $this->client->submit($form);
    }

    protected function tearDown(): void
    {
        $this->tearDownTest();
    }


}