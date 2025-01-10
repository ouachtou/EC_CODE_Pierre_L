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
        if (!$user) {
            return $this->redirectToRoute('auth.login');
        }

        $userId = $user->getId();
        $booksReading = $this->bookReadRepository->findBy(['user_id' => $userId, 'is_read' => false]);
        $booksRead  = $this->bookReadRepository->findBy(['user_id' => $userId, 'is_read' => true]);

        // Handle BookRead form submission
        $bookRead = new BookRead();
        $form = $this->createForm(BookReadFormType::class);
        $form->handleRequest($request);
        
        // if ($form->isSubmitted() && $form->isValid()) {
        //     $this->handleBookReadForm($form, $bookRead, $userId, $entityManager);
        //     $form = $this->createForm(BookReadFormType::class); // Reset form after submission
        // }

        return $this->render('pages/home.html.twig', [
            'booksRead' => $booksRead,
            'booksReading' => $booksReading,
            'name' => 'Accueil',
            'user' => $user,
            'bookReadForm' => $form->createView(),
        ]);
    }

    #[Route('/book/add', name: 'app.book.add', methods: ['POST'])]
    public function addBook(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Récupération des données du formulaire
        $data = json_decode($request->getContent(), true);

        // Get Book entity instead of just the ID
        $book = $this->bookRepository->find($data['book_read_form[book_id]']);
        
        $bookRead = new BookRead();
        $bookRead->setBookId($book);
        $bookRead->setDescription($data['book_read_form[description]']);
        $bookRead->setRating($data['book_read_form[rating]']);
        $bookRead->setIsRead($data['book_read_form[is_read]'] ?? false);
        $bookRead->setUserId($this->getUser()->getId());
        $bookRead->setCreatedAt(new \DateTime());
        $bookRead->setUpdatedAt(new \DateTime());

        $entityManager->persist($bookRead);
        $entityManager->flush();

        return $this->json($bookRead);
    }
}


