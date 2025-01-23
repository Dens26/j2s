<?php

namespace App\Controller\Admin;

use App\Classe\GameClass;
use App\Entity\Game;
use App\Service\TranslatorService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GameController extends AbstractController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/admin-game-index', name: 'admin_game_index')]
    public function index(): Response
    {
        return $this->render('admin/game/index.html.twig');
    }

    #[Route('/admin-game-create', name: 'admin_game_create')]
    public function create(Request $request): Response
    {
        // Récupérer toutes les données du formulaire
        $data = $request->request->all();

        // Définir les groupes à traiter
        $groupPrefixes = [
            'artist_',
            'designer_',
            'graphicDesigner_',
            'developper_',
            'category_',
            'subdomain_',
            'mechanic_',
            'honor_',
            'publisher_'
        ];

        // Initialiser le tableau final
        $organizedData = [
            'id' => $data['id'] ?? null,
            'name' => $data['name'] ?? null,
            'yearPublished' => $data['yearPublished'] ?? null,
            'age' => $data['age'] ?? null,
            'players' => $data['players'] ?? null,
            'playingTime' => $data['playingTime'] ?? null,
            'artists' => [],
            'designers' => [],
            'graphicDesigners' => [],
        ];

        // Parcourir les préfixes pour regrouper les données dynamiquement
        foreach ($groupPrefixes as $prefix) {
            // Déduire le nom du groupe à partir du préfixe
            $groupName = rtrim($prefix, '_') . 's'; // e.g., "designer_" devient "designers"

            // Filtrer les données pour ce groupe
            $organizedData[$groupName] = [];
            foreach ($data as $key => $value) {
                if (str_starts_with($key, $prefix)) {
                    $organizedData[$groupName][] = $value;
                }
            }
        }

        if ($request->request->count() < 7) {
            $this->addFlash('danger', 'Il faut un minimum de 5 indices');
            // Redirige vers une autre route
            return $this->redirectToRoute('admin_game_show', [
                'id' => $organizedData['id'],
                'name' => $organizedData['name']
            ]);
        }

        return $this->render('admin/game/confirm.html.twig', [
            'data' => $organizedData
        ]);
    }

    #[Route('/admin-game-show/{id}/{name}', name: 'admin_game_show')]
    public function show(int $id, string $name, TranslatorService $translatorService, EntityManagerInterface $entityManager): Response
    {
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

    #[Route('/admin-game-search', name: 'admin_game_search')]
    public function search(Request $request): Response
    {
        $games = new GameClass($this->client);
        try {
            $results = $games->SearchGames($request);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des données.');
            return [];
        }

        return $this->render('admin/game/index.html.twig', [
            'adminSearchTerm' => $results['searchTerm'],
            'results' => $results['results'],
            'page' => $results['page'],
            'totalPages' => $results['totalPages'],
            'totalResults' => $results['totalResults']
        ]);
    }
}
