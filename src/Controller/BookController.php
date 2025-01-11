<?php

namespace App\Controller;

use App\Entity\BookRead;
use App\Repository\BookReadRepository;
use App\Repository\BookRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BookController extends AbstractController
{
    private BookRepository $bookRepository;
    private BookReadRepository $bookReadRepository;

    public function __construct(BookRepository $bookRepository, BookReadRepository $bookReadRepository)
    {
        $this->bookRepository = $bookRepository;
        $this->bookReadRepository = $bookReadRepository;
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

    #[Route('/book/update', name: 'app.book.update', methods: ['PUT'])]
    public function updateBook(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);
        $bookRead = new BookRead();
        $bookRead = $this->bookReadRepository->find($data['book_read_form[id]']);
        $book = $this->bookRepository->find($data['book_read_form[book_id]']);

        $bookRead->setBookId($book);
        $bookRead->setDescription($data['book_read_form[description]']);
        $bookRead->setRating($data['book_read_form[rating]']);
        $bookRead->setIsRead($data['book_read_form[is_read]'] ?? false);
        $bookRead->setUpdatedAt(new \DateTime());
        $entityManager->flush();

        return $this->json($bookRead);
    }
}
