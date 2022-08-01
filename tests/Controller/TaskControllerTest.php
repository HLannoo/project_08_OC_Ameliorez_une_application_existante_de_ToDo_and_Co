<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
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

        $id = $this->taskRepository->findOneByTitle('TestTaskAnonymous0');
        $this->client->request('GET', '/task/'.$id->getId().'/edit');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testEditTaskPageWhenUserIsConnected(): void
    {
        $user= $this->testAnonymous;
        $id = $this->taskRepository->findOneByTitle('TestTaskAnonymous1'); // le titre a été ajouté au préalable dans les fixtures
        $formDatas = ["modifyTitle1", "modifyContent1"];
        $this->EditFormWhitDiferentUser($formDatas,$user, $id);

        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"La tâche a été mise à jour !");
    }

    public function testToggleTaskRedirectWithNoAuthorization(): void
    {
        $this->client->loginUser($this->testUser);
        $id = $this->taskRepository->findOneByTitle('TestTaskAnonymous2'); // le titre a été ajouté au préalable dans les fixtures
        $this->client->request('GET', '/task/'.$id->getId().'/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testToggleTaskRedirectWithAuthorization(): void
    {
        $this->client->loginUser($this->testAnonymous);
        $id = $this->taskRepository->findOneByTitle('TestTaskAnonymous3'); // le titre a été ajouté au préalable dans les fixtures
        $this->client->request('GET', '/task/'.$id->getId().'/toggle');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('',"Tâche mise à jour");
    }

    public function testDeleteTaskPageRedirectWhenUserIsNotConnected(): void
    {

        $id = $this->taskRepository->findOneByTitle('TestTaskAnonymous4'); // le titre a été ajouté au préalable dans les fixtures
        $this->client->request('GET', '/task/'.$id->getId().'/delete');
        $this->assertResponseStatusCodeSame(Response::HTTP_FOUND);

        $this->client->followRedirect();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testDeleteAnonymousTaskWithNoAuthorization(): void
    {
        $this->client->loginUser($this->testUser);
        $id = $this->taskRepository->findOneByTitle('TestTaskAnonymous5'); // le titre a été ajouté au préalable dans les fixtures
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

}
