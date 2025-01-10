<?php
namespace App\Controller;

use App\Repository\BookReadRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SearchController extends AbstractController {
    private BookRepository $bookRepository;
    private BookReadRepository $bookReadRepository;

    public function __construct(BookRepository $bookRepository, BookReadRepository $bookReadRepository) {
        $this->bookRepository = $bookRepository;
        $this->bookReadRepository = $bookReadRepository;
    }

    #[Route('/search', name: 'app.search')]
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
