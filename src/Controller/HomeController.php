<?php

namespace App\Controller;

use App\Entity\BookRead;
use App\Form\BookReadFormType;
use App\Repository\BookReadRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private BookReadRepository $bookReadRepository;
    private BookRepository $bookRepository;

    public function __construct(BookReadRepository $bookReadRepository, BookRepository $bookRepository)
    {
        $this->bookReadRepository = $bookReadRepository;
        $this->bookRepository = $bookRepository;
    }

    #[Route('/', name: 'app.home')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('auth.login');
        }

        $userId = $user->getId();
        $booksReading = $this->bookReadRepository->findBy(['user_id' => $userId, 'is_read' => false]);
        $booksRead  = $this->bookReadRepository->findBy(['user_id' => $userId, 'is_read' => true]);

        $form = $this->createForm(BookReadFormType::class);

        return $this->render('pages/home.html.twig', [
            'booksRead' => $booksRead,
            'booksReading' => $booksReading,
            'name' => 'Accueil',
            'user' => $user,
            'bookReadForm' => $form->createView(),
        ]);
    }
}


