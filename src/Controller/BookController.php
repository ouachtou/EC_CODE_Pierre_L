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
    // Déclaration des dépendances : BookRepository et BookReadRepository pour accéder aux livres et aux livres lus
    private BookRepository $bookRepository;
    private BookReadRepository $bookReadRepository;

    /**
     * Constructeur du contrôleur. Injection des dépendances via le constructeur.
     *
     * @param BookRepository $bookRepository Repository pour accéder aux livres.
     * @param BookReadRepository $bookReadRepository Repository pour accéder aux livres lus.
     */
    public function __construct(BookRepository $bookRepository, BookReadRepository $bookReadRepository)
    {
        $this->bookRepository = $bookRepository;
        $this->bookReadRepository = $bookReadRepository;
    }

    /**
     * Route pour ajouter un livre dans les livres lus par l'utilisateur.
     *
     * Cette méthode permet à l'utilisateur d'ajouter un livre à sa liste de livres lus. Les informations
     * sont envoyées sous forme de données JSON via une requête POST. Après l'ajout, les informations du livre
     * ajouté sont renvoyées en réponse.
     *
     * @Route("/book/add", name="app.book.add", methods={"POST"})
     *
     * @param Request $request La requête HTTP contenant les données du formulaire.
     * @param EntityManagerInterface $entityManager L'EntityManager pour gérer les entités.
     * 
     * @return Response La réponse JSON contenant les informations du livre ajouté.
     */
    public function addBook(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

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

    /**
     * Route pour mettre à jour un livre déjà ajouté dans les livres lus de l'utilisateur.
     *
     * Cette méthode permet de modifier les informations d'un livre déjà ajouté à la liste des livres lus,
     * en utilisant les données envoyées sous forme de JSON via une requête PUT. Après la mise à jour,
     * les informations mises à jour du livre sont renvoyées en réponse.
     *
     * @Route("/book/update", name="app.book.update", methods={"PUT"})
     *
     * @param Request $request La requête HTTP contenant les données de mise à jour.
     * @param EntityManagerInterface $entityManager L'EntityManager pour gérer les entités.
     * 
     * @return Response La réponse JSON contenant les informations mises à jour du livre.
     */
    public function updateBook(Request $request, EntityManagerInterface $entityManager): Response
    {
        $data = json_decode($request->getContent(), true);

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
