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
    /**
     * Route d'inscription pour l'utilisateur.
     * 
     * Cette méthode gère l'affichage du formulaire d'inscription et l'enregistrement
     * des données de l'utilisateur si le formulaire est soumis et valide.
     *
     * @Route("/register", name="auth.register")
     * 
     * @param Request $request                L'objet de requête HTTP, contenant les données soumises.
     * @param EntityManagerInterface $entityManager L'EntityManager pour persister l'utilisateur dans la base de données.
     * @param UserPasswordHasherInterface $passwordHasher L'interface pour hasher le mot de passe de l'utilisateur.
     * 
     * @return Response                        La réponse contenant soit le formulaire, soit la redirection après l'enregistrement.
     */
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

    /**
     * Gère l'enregistrement de l'utilisateur après soumission du formulaire.
     * 
     * Cette méthode effectue l'enregistrement de l'utilisateur dans la base de données,
     * y compris le hashage du mot de passe.
     *
     * @param $form                             Le formulaire d'inscription.
     * @param User $user                        L'utilisateur à enregistrer.
     * @param UserPasswordHasherInterface $passwordHasher L'interface pour hasher le mot de passe.
     * @param EntityManagerInterface $entityManager L'EntityManager pour persister l'entité dans la base de données.
     * 
     * @return void
     */
    private function handleUserRegistration($form, User $user, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): void
    {
        $hashedPassword = $passwordHasher->hashPassword($user, $form->get('password')->getData());
        
        $user->setEmail($form->get('email')->getData());
        $user->setPassword($hashedPassword);

        $entityManager->persist($user);
        $entityManager->flush();
    }
}
