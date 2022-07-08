<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
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
