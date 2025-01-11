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
    // Déclaration des dépendances : BookReadRepository, BookRepository, CategoryRepository
    private CategoryRepository $categoryRepository;
    private BookReadRepository $bookReadRepository;
    private BookRepository $bookRepository;

    /**
     * Constructeur du contrôleur. Injection des dépendances via le constructeur.
     *
     * @param BookReadRepository $bookReadRepository Repository des livres lus.
     * @param BookRepository $bookRepository Repository des livres.
     * @param CategoryRepository $categoryRepository Repository des catégories de livres.
     */
    public function __construct(BookReadRepository $bookReadRepository, BookRepository $bookRepository, CategoryRepository $categoryRepository)
    {
        $this->bookReadRepository = $bookReadRepository;
        $this->bookRepository = $bookRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Route principale de la page d'accueil.
     *
     * Cette méthode charge la page d'accueil en affichant les livres lus, en cours de lecture,
     * ainsi que des informations sur les catégories des livres de l'utilisateur.
     * Elle redirige vers la page de connexion si l'utilisateur n'est pas authentifié.
     *
     * @Route("/", name="app.home")
     *
     * @param Request $request La requête HTTP envoyée.
     * @param EntityManagerInterface $entityManager L'EntityManager pour gérer les entités.
     * 
     * @return Response La réponse contenant la vue de la page d'accueil.
     */
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Vérification si l'utilisateur est connecté
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('auth.login');
        }

        $userId = $user->getId();
        
        $booksReading = $this->bookReadRepository->findBy(['user_id' => $userId, 'is_read' => false]);
        $booksRead = $this->bookReadRepository->findBy(['user_id' => $userId, 'is_read' => true]);

        $form = $this->createForm(BookReadFormType::class);

        $categories = [];
        $allCategories = $this->categoryRepository->findAll();
        
        foreach ($allCategories as $category) {
            $categories[$category->getId()] = [
                'name' => $category->getName(),
                'count' => 0
            ];
        }
        
        // Parcours des livres lus pour compter le nombre de livres lus par catégorie
        foreach ($booksRead as $bookRead) {
            $category = $bookRead->getBookId()->getCategoryId();
            if ($bookRead->isRead()) {
                $categories[$category->getId()]['count']++;
            }
        }

        // Préparation des données pour l'affichage du graphique des catégories
        $categoryLabels = [];
        $categoryData = [];
        foreach ($categories as $category) {
            $categoryLabels[] = $category['name'];
            $categoryData[] = $category['count'];
        }

        // Rendu de la vue avec les données nécessaires
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
}
