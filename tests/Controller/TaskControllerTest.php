<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * @covers App\src\Controller\TaskController
 */
class TaskControllerTest extends WebTestCase
{
    private $client;
    private $testAdmin;
    private $testUser;
    private $userRepository;
    private $testAnonymous;


    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->userRepository = static::getContainer()->get(UserRepository::class);
        $this->testAdmin = $this->userRepository->findOneByEmail('admin-test@gmail.com'); // Il s'agit d'un administrateur
        $this->testUser = $this->userRepository->findOneByEmail('user-test@gmail.com'); // Il s'agit d'un simple utilisateur
        $this->testAnonymous = $this->userRepository->findOneByEmail('anonymous-test@gmail.com'); // Il s'agit de l'utilisateur anonyme
        $this->taskRepository = static::getContainer()->get(TaskRepository::class);
    }

    public function testTaskPageRedirectWhenUserIsNotConnected(): void
    {

        $this->client->request('GET', '/task');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }


    public function testTaskPageWhenUserIsConnected(): void
    {
        $this->client->loginUser($this->testUser);
        $this->client->request('GET', '/task');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertEquals('/task',$this->client->getRequest()->getRequestUri() );

    }

    public function testCreateTaskPageRedirectWhenUserIsNotConnected(): void
    {
        $this->client->request('GET', '/task/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testCreateTaskPageWhenUserIsConnected(): void
    {
        $this->client->loginUser($this->testAnonymous);
        $crawler = $this->client->request('GET', '/task/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $buttonName = "Ajouter";
        $this->makeForm($crawler, $buttonName);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"La tâche a été bien été ajoutée à la liste.");
        $this->assertInstanceOf(Task::class, $this->taskRepository->findOneByTitle('testTitle'));
    }

    public function testEditTaskPageRedirectWhenUserIsNotConnected(): void
    {
        $this->taskRepository->findOneByTitle('testTitle');
        $this->client->request('GET', '/task/create');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testEditTaskPageWhenUserIsConnected(): void
    {
        $this->client->loginUser($this->testAnonymous);
        $id = $this->taskRepository->findOneByTitle('testTitle');
        $crawler = $this->client->request('GET', '/task/'.$id->getId().'/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);

        $form = $crawler->selectButton('Editer')->form([
            'task[title]' => 'modifyTitle',
            'task[content]'=>'modifyContent',
        ]);
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"La tâche a été mise à jour !");
    }

    public function testToggleTaskRedirectWithNoAuthorization(): void
    {
        $this->client->loginUser($this->testUser);
        $id = $this->taskRepository->findOneByTitle('modifyTitle');
        $this->client->request('GET', '/task/'.$id->getId().'/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testToggleTaskRedirectWithAuthorization(): void
    {
        $this->client->loginUser($this->testAnonymous);
        $id = $this->taskRepository->findOneByTitle('modifyTitle');
        $this->client->request('GET', '/task/'.$id->getId().'/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"Tâche mise à jour");
    }

    public function testDeleteTaskPageRedirectWhenUserIsNotConnected(): void
    {
        $id = $this->taskRepository->findOneByTitle('modifyTitle');
        $this->client->request('GET', '/task/'.$id->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testDeleteAnonymousTaskWithNoAuthorization(): void
    {
        $this->client->loginUser($this->testUser);
        $id = $this->taskRepository->findOneByTitle('modifyTitle');
        $this->client->request('GET', '/task/'.$id->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
        $this->assertSelectorTextContains('',"Vous ne disposez pas des droits requis pour réaliser cette action");
    }

    public function testDeleteAnonymousTaskWhenUserIsAdmin(): void
    {
        $this->client->loginUser($this->testAdmin);
        $id = $this->taskRepository->findOneByTitle('modifyTitle');
        $this->client->request('GET', '/task/'.$id->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"La tâche {$id->getTitle()} a été supprimé avec succès !");
    }

    private function makeForm($crawler, $buttonName)
    {
        $form = $crawler->selectButton($buttonName)->form([
            'task[title]' => "testTitle",
            'task[content]'=> "testContent",
        ]);
        $this->client->submit($form);
    }
    public function tearDown(): void
    {

    }
}
