<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TaskController extends AbstractController
{
    public function __construct(protected TaskRepository $taskRepository, protected EntityManagerInterface $em)
    {
        $this->taskRepository = $taskRepository;
        $this->em = $em;
    }

    #[Route('/task', name: 'task_list')]
    public function index(): Response
    {
        return $this->render('task/index.html.twig', [
            'tasks' => $this->taskRepository->findAll()]);
    }


    #[Route('/task/create', name: 'task_create')]
    public function create(Request $request)
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
            return $this->redirectToRoute('task_list');
        }
        return $this->render('task/create_update.html.twig', [
            'form' => $form->createView()]);


    }

    #[Route('/task/{id}/edit', name: 'task_edit')]
    public function edit($id, Request $request)
    {
        $task = $this->taskRepository->findOneBy(['id' => $id]);
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
                $task->setIsDone(false);
            // $task->setCurrentUser($this->getUser());

            $this->em->flush();
            return $this->redirectToRoute('task_list');
        }

        return $this->render('task/create_update.html.twig', [
            'task' => $task,
            'form' => $form->createView()
        ]);
    }

    #[Route('task/{id}/delete', name: 'task_delete')]
    public function delete($id): response
    {
        $task = $this->taskRepository->findOneBy(['id' => $id]);
        $this->em->remove($task);
        $this->em->flush();

        $this->addflash(
            'success',
            "La tâche {$task->getTitle()} a été supprimé avec succès !"
        );

        return $this->redirectToRoute('task_list');

    }

    #[Route('task/{id}/toggle', name: 'task_toggle')]
    public function toggleTaskAction($id)
    {
        $task = $this->taskRepository->findOneBy(['id' => $id]);

        if ($task->isIsDone() === false) {
            $task->setIsDone(true);
        }
        elseif ($task->isIsDone()=== true){
            $task->setIsDone(false);
        }
        $this->em->flush();

        $this->addFlash('success',"Tâche mise à jour");

        return $this->redirectToRoute('task_list');
    }


}
