<?php

namespace App\Controller;

use App\Form\LoginType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class SecurityController extends AbstractController
{
    public function __construct(Protected UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    #[Route('/login', name: 'security_login')]
    public function login(AuthenticationUtils $utils): Response
    {
        $form = $this->createForm(LoginType::class);
        $errorMessage = "";
        $error = $utils->getLastAuthenticationError();
        if (!is_null($error)) {
            $errorMessage = $error->getMessage();
        }

        return $this->render('security/login.html.twig', [
            'formView'=>$form->createView(),
            'error'=>$errorMessage,
            'success'=>$utils->getLastUsername(),
        ]);
    }

    #[Route('/logout', name: 'security_logout')]
    public function logout()
    {
    }


}
