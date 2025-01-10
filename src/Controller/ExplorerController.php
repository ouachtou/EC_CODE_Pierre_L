<?php

namespace App\Controller;

use App\Repository\BookReadRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ExplorerController extends AbstractController
{
    private BookReadRepository $bookReadRepository;

    public function __construct(BookReadRepository $bookReadRepository)
    {
        $this->bookReadRepository = $bookReadRepository;
    }

    #[Route('/explorer', name: 'app_explorer')]
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
