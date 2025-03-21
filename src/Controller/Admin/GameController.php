<?php

namespace App\Controller\Admin;

use App\Classe\GameClass;
use App\Entity\Game;
use App\Entity\MysteryGame;
use App\Service\TranslatorService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GameController extends AbstractController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/admin-game-index', name: 'admin_game_index')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $mysteryGames = $entityManager->getRepository(MysteryGame::class)
            ->createQueryBuilder('m')
            ->where('m.status IN (:statuses)')
            ->setParameter('statuses', [1, 4])
            ->getQuery()
            ->getResult();

        return $this->render('admin/game/search.html.twig', [
            'mysteryGames' => $mysteryGames
        ]);
    }

    #[Route('/admin-game-show/{id}/{name}', name: 'admin_game_show')]
    public function show(int $id, string $name, TranslatorService $translatorService, EntityManagerInterface $entityManager, Request $request): Response
    {
        $mysteryGame = $entityManager->getRepository(MysteryGame::class)->findOneBy(['name' => $name]);

        if ($mysteryGame) {
            $this->addFlash('danger', 'Ce jeu à déjà été choisi comme jeu mystère. Merci d\'en sélectionner un autre !');
            return $this->redirectToRoute('admin_game_index');
        }
        // Check if the game exist in the database
        $gameClass = new GameClass($this->client);
        $game = $entityManager->getRepository(Game::class)->findOneBy(['gameId' => $id]);
        if ($game) {
            return $this->render('admin/game/show.html.twig', [
                'results' => $gameClass->formatGame($game)
            ]);
        }

        $result = $gameClass->ShowGame($entityManager, $id, $name, $translatorService);

        return $this->render('admin/game/show.html.twig', [
            'results' => $gameClass->formatGame($result)
        ]);
    }

    #[Route('/admin-game-search', name: 'admin_game_search', methods: ['GET'])]
    public function search(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $mysteryGame = $entityManager->getRepository(MysteryGame::class)->findOneBy(['status' => 'stream']);
        $games = new GameClass($this->client);
        try {
            $results = $games->SearchGames($request, $slugger);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des données.');
            return [];
        }

        return $this->render('admin/game/search.html.twig', [
            'adminSearchTerm' => $results['searchTerm'],
            'results' => $results['results'],
            'page' => $results['page'],
            'totalPages' => $results['totalPages'],
            'totalResults' => $results['totalResults'],
            'mysteryGame' => $mysteryGame
        ]);
    }
}
