<?php

namespace App\Controller\navbar;

use App\Classe\GameClass;
use App\Entity\Game;
use App\Service\TranslatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SearchController extends AbstractController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }


    #[Route('/boardgame-search', name: 'app_boardgame_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $games = new GameClass($this->client);

        try {
            $results = $games->SearchGames($request);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des données.');
            return [];
        }

        return $this->render('base.html.twig', [
            'searchTerm' => $results['searchTerm'],
            'results' => $results['results'],
            'page' => $results['page'],
            'totalPages' => $results['totalPages'],
            'totalResults' => $results['totalResults']
        ]);
    }


    #[Route('/boardgame-show/{id}/{name}', name: 'app_boardgame_show', requirements: ['id' => '\d+', 'name' => '.+'], methods: ['GET'])]
    public function show(int $id, string $name, EntityManagerInterface $entityManager, TranslatorService $translatorService): Response
    {
        // Check if the game exist in the database
        $gameClass = new GameClass($this->client);
        $game = $entityManager->getRepository(Game::class)->findOneBy(['gameId' => $id]);
        if ($game) {
            return $this->render('pages/search/show.html.twig', [
                'results' => $gameClass->formatGame($game)
            ]);
        }
        $game = new GameClass($this->client);
        $result = $game->ShowGame($entityManager, $id, $name, $translatorService);

        return $this->render('pages/search/show.html.twig', [
            'results' => $gameClass->formatGame($result)
        ]);
    }
}
