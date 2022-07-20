<?php

namespace App\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class UserController extends AbstractController
{
    public function __construct(protected UserRepository $userRepository, protected EntityManagerInterface $em, protected UserPasswordHasherInterface $passwordHasher, protected  EmailVerifier $emailVerifier)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->passwordHasher = $passwordHasher;
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/user', name: 'user_list')]
    #[Security("is_granted('CAN_VIEW', user)")]
    public function index(): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $this->userRepository->findAll()]);
    }

    #[Route('/user/create', name: 'user_create')]
    #[Security("is_granted('CAN_CREATE', user)")]
    public function createUser(Request $request)
    {
        $this->denyAccessUnlessGranted('CAN_CREATE', $this->getUser());
        $user = New User();
        $form = $this->createForm(UserType::CLASS, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()){
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $form->get('password')->getData()))
                ->setRoles($form->get('roles')->getData())
                ->setIsVerified(1);

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', "L'utilisateur a bien été ajouté");
            return $this->redirectToRoute('user_list');
        }
        return $this->render('user/create_update.html.twig',
            ['form' => $form->createView()]);

    }
        #[Route('/user/{id}/edit ', name: 'user_edit')]
        #[Security("is_granted('CAN_EDIT', user)")]
        public function editUser(Request $request, $id)
        {
            $user = $this->userRepository->findOneBy(['id' => $id]);
            $form = $this->createForm(UserType::class, $user);
            $form->remove("password");
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setRoles($form->get('roles')->getData());

                $this->em->flush();
                $this->addFlash('success', "L'utilisateur a bien été modifié");

                return $this->redirectToRoute('user_list');

            }
            return $this->render('user/create_update.html.twig', ['form' => $form->createView(), 'user' => $user]);
        }

    #[Route('user/{id}/delete', name: 'user_delete')]
    #[Security("is_granted('CAN_DELETE', user)")]
    public function deleteTask($id, User $user, TaskRepository $taskRepository, Task $task): response
    {
        $users = $this->userRepository->findOneBy(['id' => $id]);
        $tasks = $taskRepository->findBy(array('currentUser' => $users));
        $anonymousUser = $this->userRepository->findOneBy(['username' => "Anonyme"]);

        foreach ($tasks as $task){
            $task->setCurrentUser($anonymousUser);
            $this->em->persist($task);
        }
        $this->em->remove($users);
        $this->em->flush();

        $this->addflash(
            'success',
            "Le user {$users->getUsername()} a été supprimé avec succès !"
        );

        return $this->redirectToRoute('user_list');

    }

}
