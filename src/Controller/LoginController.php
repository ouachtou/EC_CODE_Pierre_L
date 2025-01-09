<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'auth.login')]
    public function login(): Response
    {
        
        // Render the 'hello.html.twig' template
        return $this->render('auth/login.html.twig', [
            'name' => 'Thibaud', // Pass data to the view
        ]);
    }
}
