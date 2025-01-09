<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Form\RegisterFormType;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'auth.register')]
    public function register(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(RegisterFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleUserRegistration($form, $user, $passwordHasher, $entityManager);
            return $this->redirectToRoute('auth.login');
        }

        return $this->render('auth/register.html.twig', [
            'RegisterForm' => $form->createView(),
        ]);
    }

    private function handleUserRegistration($form, User $user, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): void
    {
        $hashedPassword = $passwordHasher->hashPassword($user, $form->get('password')->getData());
        $user->setEmail($form->get('email')->getData());
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();
    }
}
