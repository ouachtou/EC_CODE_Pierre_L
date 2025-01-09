<?php

namespace App\Controller;

use App\Entity\BookRead;
use App\Form\BookReadFormType;
use App\Repository\BookReadRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    // Inject the repository via the constructor
    public function __construct(BookReadRepository $bookReadRepository, BookRepository $bookRepository)
    {
        $this->bookReadRepository = $bookReadRepository;
        $this->bookRepository = $bookRepository;
    }

    #[Route('/', name: 'app.home')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $userId = $user->getId();
        if (!$user) {
            return $this->redirectToRoute('auth.login');
        }
        $booksRead  = $this->bookReadRepository->findBy(['user_id' => $userId, 'is_read' => true]);
        $booksReading = $this->bookReadRepository->findBy(['user_id' => $userId, 'is_read' => false]);

        $bookRead = new BookRead();
        $form = $this->createForm(BookReadFormType::class);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $bookRead->setIsRead($form->get('is_read')->getData());
            $bookRead->setRating($form->get('rating')->getData());
            $bookRead->setDescription($form->get('description')->getData());
            $bookRead->setBookId($form->get('book_id')->getData());
            $bookRead->setUserId($userId);
            $bookRead->setCreatedAt(new \DateTime());
            $bookRead->setUpdatedAt(new \DateTime());
            $entityManager->persist($bookRead);
            $entityManager->flush();
            $form = $this->createForm(BookReadFormType::class);
        }

        return $this->render('pages/home.html.twig', [
            'booksRead' => $booksRead,
            'booksReading' => $booksReading,
            'name'      => 'Accueil',
            'user'    => $user,
            'bookReadForm' => $form->createView(),
        ]);
    }
}

