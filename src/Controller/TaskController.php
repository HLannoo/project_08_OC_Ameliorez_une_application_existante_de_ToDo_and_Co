<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

class TaskController extends AbstractController
{
    public function __construct(protected TaskRepository $taskRepository, protected EntityManagerInterface $em, protected UserRepository $userRepository, CacheInterface $cache)
    {
        $this->taskRepository = $taskRepository;
        $this->em = $em;
        $this->userRepository = $userRepository;
        $this->cache = $cache;
    }

    #[Route('/task', name: 'task_list')]
    public function index(): Response
    {
        $tasks = $this->cache->get('task_list', function(){
            return $this->taskRepository->findBy(array(), array('isDone' => 'ASC','createdAt'=>'DESC'));
        });

        return $this->render('task/index.html.twig', [
            'tasks' => $tasks
        ]);
    }


    #[Route('/task/create', name: 'task_create')]
    public function createTaskForm(Request $request)
    {
        $task = new Task();
        $form = $this->createForm(TaskType::class,$task);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $task->setCreatedAt(new \DateTimeImmutable())
                ->setIsDone(false)
                ->setCurrentUser($this->getUser());

            $this->em->persist($task);
            $this->em->flush();

            $this->addFlash('success', 'La tâche a été bien été ajoutée à la liste.');
            $this->cache->delete('task_list');
            return $this->redirectToRoute('task_list');
        }
        return $this->render('task/create_update.html.twig', [
            'form' => $form->createView()]);


    }

    #[Route('/task/{id}/edit', name: 'task_edit')]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('TASK_EDIT', task)")]
    public function editTask($id, Request $request,Task $task)
    {
        $tasks = $this->taskRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(TaskType::class, $tasks);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $task->setCreatedAt(new \DateTimeImmutable());
            $this->em->flush();

            $this->addflash(
                'success',
                "La tâche a été mise à jour !"
            );
            $this->cache->delete('task_list');
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create_update.html.twig', [
            'task' => $tasks,
            'form' => $form->createView()
        ]);
    }

    #[Route('task/{id}/delete', name: 'task_delete')]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('TASK_DELETE', task)")]
    public function deleteTask($id, Task $task): response
    {

        $tasks = $this->taskRepository->findOneBy(['id' => $id]);
        $this->em->remove($tasks);
        $this->em->flush();

        $this->addflash(
            'success',
            "La tâche {$tasks->getTitle()} a été supprimé avec succès !"
        );
        $this->cache->delete('task_list');
        return $this->redirectToRoute('task_list');
    }

    #[Route('task/{id}/toggle', name: 'task_toggle')]
    #[Security("is_granted('ROLE_ADMIN') or is_granted('TASK_TOGGLE', task)")]
    public function toggleTaskAction($id, Task $task)
    {
        $tasks = $this->taskRepository->findOneBy(['id' => $id]);

        if ($tasks->isIsDone() === false) {
            $tasks->setIsDone(true);
        }
        elseif ($tasks->isIsDone()=== true){
            $tasks->setIsDone(false);
        }
        $this->em->flush();

        $this->addFlash('success',"Tâche mise à jour");
        $this->cache->delete('task_list');
        return $this->redirectToRoute('task_list');
    }


}
