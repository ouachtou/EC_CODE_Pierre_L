<?php

namespace App\Controller;

use App\Repository\BookReadRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController {
    // Déclaration des dépendances : BookRepository et BookReadRepository
    private BookRepository $bookRepository;
    private BookReadRepository $bookReadRepository;

    /**
     * Constructeur du contrôleur. Injection des dépendances via le constructeur.
     *
     * @param BookRepository $bookRepository Le repository des livres.
     * @param BookReadRepository $bookReadRepository Le repository des livres lus (avec les évaluations).
     */
    public function __construct(BookRepository $bookRepository, BookReadRepository $bookReadRepository) {
        $this->bookRepository = $bookRepository;
        $this->bookReadRepository = $bookReadRepository;
    }

    /**
     * Route permettant de rechercher des livres via une requête de recherche.
     *
     * Cette méthode prend la chaîne de recherche passée via la requête HTTP et
     * effectue une recherche dans la base de données des livres.
     * Elle renvoie ensuite les résultats sous forme de réponse JSON.
     *
     * @Route("/search", name="app.search")
     * 
     * @param Request $request La requête HTTP contenant la chaîne de recherche.
     * @return JsonResponse Les résultats de la recherche formatés en JSON.
     */
    #[Route("/search", name: 'app.search')]
    public function search(Request $request): JsonResponse
    {
        $query = $request->query->get('query');
        
        $searchBooks = $this->bookRepository->createQueryBuilder('book')
            ->where('book.name LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->getQuery()
            ->getResult();

        $results = $this->formatSearchResults($searchBooks);

        return new JsonResponse($results);
    }

    /**
     * Formate les résultats de la recherche des livres.
     * Pour chaque livre trouvé, récupère également les évaluations associées et calcule la note moyenne.
     *
     * @param array $searchBooks Les livres trouvés par la recherche.
     * @return array Les résultats formatés (nom, description, couverture, note).
     */
    private function formatSearchResults(array $searchBooks): array
    {
        $results = [];
        
        foreach ($searchBooks as $book) {
            $bookRead = $this->bookReadRepository->findBy(['book_id' => $book->getId()]);
            
            $results[] = [
                'name' => $book->getName(), 
                'description' => $book->getDescription(),
                'cover' => $book->getCover(),
                'rating' => count($bookRead) > 0 ? array_sum(array_map(fn($br) => $br->getRating(), $bookRead)) / count($bookRead) : 0,
            ];
        }
        return $results;
    }
}
