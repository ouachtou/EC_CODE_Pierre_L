<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use App\Repository\UserRepository;

class LoginController extends AbstractController
{
    #[Route(path: '/login', name: 'auth.login')]
    public function login(AuthenticationUtils $authenticationUtils, UserRepository $userRepository): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        $userExists = $userRepository->findOneBy(['email' => $lastUsername]);

        if (empty($lastUsername)) {
            $error = 'Nom d\'utilisateur ou mot de passe invalide.';
        } elseif ($userExists && $error) {
            $error = 'Mot de passe incorrect.';
        } elseif (!$userExists) {
            $error = 'Nom d\'utilisateur ou mot de passe invalide.';
        }
        
        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
