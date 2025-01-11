<?php

namespace App\Controller;

use App\Repository\BookReadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExplorerController extends AbstractController
{
    private BookReadRepository $bookReadRepository;

    /**
     * Constructeur du contrôleur. Injection de la dépendance BookReadRepository via le constructeur.
     *
     * @param BookReadRepository $bookReadRepository Repository pour accéder aux livres lus.
     */
    public function __construct(BookReadRepository $bookReadRepository)
    {
        $this->bookReadRepository = $bookReadRepository;
    }

    /**
     * Route principale pour l'exploration des livres lus.
     *
     * Cette méthode est responsable de l'affichage de la page d'exploration où l'utilisateur peut
     * consulter les livres qui ont été lus. Elle récupère tous les livres lus via le repository
     * et les passe à la vue Twig pour affichage.
     *
     * @Route("/explorer", name="app_explorer")
     *
     * @return Response La réponse contenant la vue de la page d'exploration avec les livres lus.
     */
    #[Route("/explorer", name: 'app_explorer')]
    public function index(): Response
    {
        $booksRead = $this->bookReadRepository->findAll();

        $user = $this->getUser();

        return $this->render('pages/explorer.html.twig', [
            'booksRead' => $booksRead,
            'user' => $user,
        ]);
    }
}
