<?php

namespace App\Controller;

use App\Entity\BookRead;
use App\Form\BookReadFormType;
use App\Repository\BookReadRepository;
use App\Repository\BookRepository;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    private CategoryRepository $categoryRepository;
    private BookReadRepository $bookReadRepository;
    private BookRepository $bookRepository;

    // Inject the repository via the constructor
    public function __construct(BookReadRepository $bookReadRepository, BookRepository $bookRepository, CategoryRepository $categoryRepository)
    {
        $this->bookReadRepository = $bookReadRepository;
        $this->bookRepository = $bookRepository;
        $this->categoryRepository = $categoryRepository;
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
        
        if ($form->isSubmitted() && $form->isValid()) {
            $this->handleBookReadForm($form, $bookRead, $userId, $entityManager);
            $form = $this->createForm(BookReadFormType::class); // Reset form after submission
        }

        $categories = [];
        $allCategories = $this->categoryRepository->findAll();
        foreach ($allCategories as $category) {
            $categories[$category->getId()] = [
                'name' => $category->getName(),
                'count' => 0
            ];
        }
        foreach ($booksRead as $bookRead) {
            $category = $bookRead->getBookId()->getCategoryId();
            if ($bookRead->isRead()) {
                $categories[$category->getId()]['count']++;
            }
        }
        $categoryLabels = [];
        $categoryData = [];
        foreach ($categories as $category) {
            $categoryLabels[] = $category['name'];
            $categoryData[] = $category['count'];
        }

        return $this->render('pages/home.html.twig', [
            'booksRead' => $booksRead,
            'booksReading' => $booksReading,
            'name' => 'Accueil',
            'user' => $user,
            'bookReadForm' => $form->createView(),
            'categoryLabels' => $categoryLabels,
            'categoryData' => $categoryData,
        ]);
    }


    // Helper method to handle BookRead form submission
    private function handleBookReadForm($form, BookRead $bookRead, int $userId, EntityManagerInterface $entityManager): void
    {
        $bookRead->setIsRead($form->get('is_read')->getData());
        $bookRead->setRating($form->get('rating')->getData());
        $bookRead->setDescription($form->get('description')->getData());
        $bookRead->setBookId($form->get('book_id')->getData());
        $bookRead->setUserId($userId);
        $bookRead->setCreatedAt(new \DateTime());
        $bookRead->setUpdatedAt(new \DateTime());

        $entityManager->persist($bookRead);
        $entityManager->flush();
    }
}

