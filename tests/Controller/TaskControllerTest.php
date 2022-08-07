<?php

namespace App\Tests\Controller;


use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Tests\Fixtures\ToDoFixturesTest;
use Symfony\Component\HttpFoundation\Response;


class TaskControllerTest extends ToDoFixturesTest
{
    private $client;
    private $testAdmin;
    private $testUser;
    private $userRepository;
    private $testAnonymous;

    public function setUp(): void
    {
        $this->initializeTest();
        self::ensureKernelShutdown();
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
        $formDatas = ["testTitle1", "testContent1"];
        $user = $this->testUser;
        $this->CreateFormWhitDiferentUser($formDatas,$user);
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"La tâche a été bien été ajoutée à la liste.");
        $this->assertInstanceOf(Task::class, $this->taskRepository->findOneByTitle('testTitle1'));
    }


    public function testEditTaskPageRedirectWhenUserIsNotConnected(): void
    {
        $formDatas = ["testTitle2", "testContent2"];
        $user = $this->testUser;
        $this->CreateFormWhitDiferentUser($formDatas,$user);
        $this->client->request('GET', '/logout');

        $id = $this->taskRepository->findOneByTitle('testTitle2');
        $this->client->request('GET', '/task/'.$id->getId().'/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }


    public function testEditTaskPageWhenUserIsConnected(): void
    {
        $formDatas = ["testTitle3", "testContent3"];
        $user= $this->testAnonymous;
        $this->CreateFormWhitDiferentUser($formDatas,$user);
        $id = $this->taskRepository->findOneByTitle('testTitle3');

        $formDatas = ["testTitleModify3", "testContentModify3"];
        $this->EditFormWhitDiferentUser($formDatas,$user, $id);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"La tâche a été mise à jour !");
    }


    public function testToggleTaskRedirectWithNoAuthorization(): void
    {
        $formDatas = ["testTitle4", "testContent4"];
        $user= $this->testAnonymous;
        $this->CreateFormWhitDiferentUser($formDatas,$user);
        $this->client->request('GET', '/logout');

        $this->client->loginUser($this->testUser);
        $id = $this->taskRepository->findOneByTitle('TestTitle4');
        $this->client->request('GET', '/task/'.$id->getId().'/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }


    public function testToggleTaskRedirectWithAuthorization(): void
    {
        $user = $this->testAnonymous;
        $formDatas = ["testTitle5", "testContent5"];
        $this->CreateFormWhitDiferentUser($formDatas,$user);

        $id = $this->taskRepository->findOneByTitle('TestTitle5');
        $this->client->request('GET', '/task/'.$id->getId().'/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"Tâche mise à jour");
    }


    public function testDeleteTaskPageRedirectWhenUserIsNotConnected(): void
    {
        $user = $this->testAnonymous;
        $formDatas = ["testTitle6", "testContent6"];
        $this->CreateFormWhitDiferentUser($formDatas,$user);
        $this->client->request('GET', '/logout');

        $id = $this->taskRepository->findOneByTitle('TestTitle6');
        $this->client->request('GET', '/task/'.$id->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }


    public function testDeleteAnonymousTaskWithNoAuthorization(): void
    {
        $user = $this->testAnonymous;
        $formDatas = ["testTitle7", "testContent7"];
        $this->CreateFormWhitDiferentUser($formDatas,$user);
        $this->client->request('GET', '/logout');

        $this->client->loginUser($this->testUser);
        $id = $this->taskRepository->findOneByTitle('TestTitle7');
        $this->client->request('GET', '/task/'.$id->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
        $this->assertSelectorTextContains('',"Vous ne disposez pas des droits requis pour réaliser cette action");
    }


    public function testDeleteAnonymousTaskWhenUserIsAdmin(): void
    {
        $user = $this->testAnonymous;
        $formDatas = ["testTitleAdmin", "testContentAdmin"];
        $this->CreateFormWhitDiferentUser($formDatas,$user);
        $this->client->request('GET', '/logout');

        $this->client->loginUser($this->testAdmin);
        $id = $this->taskRepository->findOneByTitle('testTitleAdmin');
        $this->client->request('GET', '/task/'.$id->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"La tâche {$id->getTitle()} a été supprimé avec succès !");
    }

    private function CreateFormWhitDiferentUser($formDatas, $user)
    {
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', '/task/create');
        $form = $crawler->selectButton("Ajouter")->form([
            'task[title]' => $formDatas[0],
            'task[content]'=> $formDatas[1],
        ]);
        return $this->client->submit($form);
    }

    private function EditFormWhitDiferentUser($formDatas, $user, $id)
    {
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', '/task/'.$id->getId().'/edit');
        $form = $crawler->selectButton("Editer")->form([
            'task[title]' => $formDatas[0],
            'task[content]'=> $formDatas[1],
        ]);
        return $this->client->submit($form);
    }

    protected function tearDown(): void
    {
        $this->tearDownTest();
    }

}
