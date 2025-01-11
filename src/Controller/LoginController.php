<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller responsable de la gestion de la page de connexion.
 */
class LoginController extends AbstractController
{
    /**
     * Route pour afficher et gérer la page de connexion.
     *
     * @param AuthenticationUtils $authenticationUtils Outil pour gérer les erreurs d'authentification et récupérer
     *                                                 le dernier nom d'utilisateur utilisé lors de la tentative de connexion.
     *
     * @return Response Retourne la réponse contenant le rendu de la page de connexion.
     */
    #[Route(path: '/login', name: 'auth.login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();

        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }
}
