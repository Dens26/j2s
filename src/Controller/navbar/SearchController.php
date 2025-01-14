<?php

namespace App\Controller\navbar;

use App\Entity\Game;
use App\Entity\Publisher;
use App\Repository\GameRepository;
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
        $searchTerm = $request->query->get('search', '');
        $page = (int)$request->query->get('page', 1);
        $resultsPerPage = 10;

        $data = $this->fetchBoardGameGeekData($searchTerm, $page, $resultsPerPage);
        $results = $data['results'];
        $totalPages = $data['totalPages'];
        $totalResults = $data['totalResults'];

        return $this->render('base.html.twig', [
            'searchTerm' => $searchTerm,
            'results' => $results,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalResults' => $totalResults
        ]);
    }

    #[Route('/boardgame-show/{id}/{name}', name: 'app_boardgame_show', requirements: ['id' => '\d+', 'name' => '.+'], methods: ['GET'])]
    public function show(int $id, string $name, EntityManagerInterface $entityManager): Response
    {
        // Vérification si le jeu existe en base de données
        $game = $entityManager->getRepository(GameRepository::class)->findOneBy(['gameId' => $id]);
        if ($game) {
        }

        // Le jeu n'existe pas : Appel à l'API
        $url = "https://boardgamegeek.com/xmlapi/boardgame/$id";
        $response = $this->client->request('GET', $url);
        $content = $response->getContent();
        $xml = new \SimpleXMLElement($content);

        // Mapping des données du jeu
        $results = [
            'name' => (string)$name,
            'names' => $xml->boardgame->name ? json_encode((array)$xml->boardgame->name) : '[]',
            'yearPublished' => (string)$xml->boardgame->yearpublished ?? null,
            'minPlayers' => (string)$xml->boardgame->minplayers ?? null,
            'maxPlayers' => (string)$xml->boardgame->maxplayers ?? null,
            'playingTime' => (string)$xml->boardgame->playingtime ?? null,
            // 'minPlayTime' => (string)$xml->boardgame->minplaytime ?? null,
            // 'maxPlayTime' => (string)$xml->boardgame->maxplaytime ?? null,
            'age' => (string)$xml->boardgame->age ?? null,
            'description' => (string)$xml->boardgame->description ?? null,
            'thumbnail' => (string)$xml->boardgame->thumbnail ?? null,
            'image' => (string)$xml->boardgame->image ?? null,
            'publishers' => $xml->boardgame->boardgamepublisher ? (array)$xml->boardgame->boardgamepublisher : [],
            // 'versions' => $xml->boardgame->boardgameversion ? (array)$xml->boardgame->boardgameversion : [],
            'artists' => $xml->boardgame->boardgameartist ? (array)$xml->boardgame->boardgameartist : [],
            'categories' => $xml->boardgame->boardgamecategory ? (array)$xml->boardgame->boardgamecategory : [],
            'subdomains' => $xml->boardgame->boardgamesubdomain ? (array)$xml->boardgame->boardgamesubdomain : [],
            'mechanics' => $xml->boardgame->boardgamemechanic ? (array)$xml->boardgame->boardgamemechanic : [],
            'designers' => $xml->boardgame->boardgamedesigner ? (array)$xml->boardgame->boardgamedesigner : [],
            'graphicDesigners' => $xml->boardgame->boardgamegraphicdesigner ? (array)$xml->boardgame->boardgamegraphicdesigner : [],
            'developers' => $xml->boardgame->boardgamedeveloper ? (array)$xml->boardgame->boardgamedeveloper : [],
            'honor' => $xml->boardgame->boardgamehonor ? (array)$xml->boardgame->boardgamehonor : [],
            'families' => $xml->boardgame->boardgamefamily ? (array)$xml->boardgame->boardgamefamily : [],
        ];
        /*
            Translate description with DeepL API Free
        */

        // Création du jeu
        $game = new Game();
        $game
        ->setGameId($id)
        ->setName($results['name'])
        ->setAllNames($results['names'])
        ->setYearPublished($results['yearPublished'])
        ->setMinPlayers($results['minPlayers'])
        ->setMaxPlayers($results['maxPlayers'])
        ->setPlayingTime($results['playingTime'])
        ->setAge($results['age'])
        ->setDescription($results['description'])
        ->setThumbnail($results['thumbnail'])
        ->setImage($results['image'])
        ;

        $entityManager->persist($game);

        // Gestion des table liées
        $this->handleRelateData($results['publishers'], $game, Publisher::class, 'addPublisher', $entityManager);
        $this->handleRelateData($results['artists'], $game, Publisher::class, 'addArtist', $entityManager);
        $this->handleRelateData($results['categories'], $game, Publisher::class, 'addCategory', $entityManager);
        $this->handleRelateData($results['subdomains'], $game, Publisher::class, 'addSubdomain', $entityManager);
        $this->handleRelateData($results['mechanics'], $game, Publisher::class, 'addMechanic', $entityManager);
        $this->handleRelateData($results['designers'], $game, Publisher::class, 'addDesigner', $entityManager);
        $this->handleRelateData($results['graphicDesigners'], $game, Publisher::class, 'addGraphicDesigner', $entityManager);
        $this->handleRelateData($results['developers'], $game, Publisher::class, 'addDeveloper', $entityManager);
        $this->handleRelateData($results['honor'], $game, Publisher::class, 'addHonor', $entityManager);
        $this->handleRelateData($results['families'], $game, Publisher::class, 'addFamily', $entityManager);

        // Sauvegarde des données
        $entityManager->flush();

        return $this->render('pages/search/show.html.twig', [
            'results' => $results
        ]);
    }


    private function fetchBoardGameGeekData(string $searchTerm, int $page, int $resultsPerPage): array
    {
        $url = 'https://boardgamegeek.com/xmlapi/search?search=' . urlencode($searchTerm);
        $results = [];
        $totalResults = 0;

        try {
            $response = $this->client->request('GET', $url);
            $content = $response->getContent();
            $xml = new \SimpleXMLElement($content);

            // Compter le nombre total de résultats trouvés
            $totalResults = count($xml->boardgame);

            // Calcul du début et de la fin des résultats à afficher pour cette page
            $start = ($page - 1) * $resultsPerPage;
            $end = $start + $resultsPerPage;
            $counter = 0;

            foreach ($xml->boardgame as $game) {
                if ($counter >= $start && $counter < $end) {
                    $id = (string) $game['objectid'];
                    $name = (string) $game->name;
                    $yearPublished = (string) $game->yearpublished ?: 'N/A';

                    // Récupérer le thumbnail
                    $thumbnail = $this->fetchThumbnail($id);

                    $results[] = [
                        'id' => $id,
                        'name' => $name,
                        'thumbnail' => $thumbnail
                    ];
                }
                $counter++;
                if ($counter >= $end) break; // Arrêter dès qu'on a atteint le max pour la page
            }

            // Calculer le nombre total de pages
            $totalPages = (int) ceil($totalResults / $resultsPerPage);

            return [
                'results' => $results,
                'totalPages' => $totalPages,
                'totalResults' => $totalResults
            ];
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des données.');
            return [];
        }
    }

    private function fetchThumbnail(string $gameId): ?string
    {
        try {
            $url = "https://boardgamegeek.com/xmlapi/boardgame/$gameId";
            $response = $this->client->request('GET', $url);
            $content = $response->getContent();
            $xml = new \SimpleXMLElement($content);

            // Le `thumbnail` est directement un enfant de `boardgame`
            return (string) $xml->boardgame->thumbnail ?? null;
        } catch (\Exception $e) {
            return null; // Si l'image n'est pas trouvée, retourner null
        }
    }

    private function formatGame(Game $game) : array
    {
        return [
            'id' => $game->getGameId(),
            'name' => $game->getName(),
            'names' => json_decode($game->getAllNames(), true) ?? [],
            'yearPublished' => $game->getYearPublished(),
            'minPlayers' => $game->getMinPlayers(),
            'maxPlayers' => $game->getMaxPlayers(),
            'playingTime' => $game->getPlayingTime(),
            'age' => $game->getAge(),
            'description' => $game->getDescription(),
            'thumbnail' => $game->getThumbnail(),
            'image' => $game->getImage(),
            'publishers' => $game->getPublishers()->map(fn($publisher) => $publisher->getName())->toArray(),
            'artists' => $game->getArtists()->map(fn($artist) => $artist->getName())->toArray(),
            'categories' => $game->getCategories()->map(fn($category) => $category->getName())->toArray(),
            'subdomains' => $game->getDesigners()->map(fn($designer) => $designer->getName())->toArray(),
            'mechanics' => $game->getDesigners()->map(fn($designer) => $designer->getName())->toArray(),
            'designers' => $game->getDesigners()->map(fn($designer) => $designer->getName())->toArray(),
            'graphicDesigners' => $game->getDesigners()->map(fn($designer) => $designer->getName())->toArray(),
            'developers' => $game->getDesigners()->map(fn($designer) => $designer->getName())->toArray(),
            'honors' => $game->getDesigners()->map(fn($designer) => $designer->getName())->toArray(),
            'families' => $game->getDesigners()->map(fn($designer) => $designer->getName())->toArray(),
        ];
    }

    private function handleRelateData(array $data, Game $game, string $entityClass, string $addMethod, EntityManagerInterface $entityManager) :void
    {
        foreach ($data as $item){
            // Vérification si l'élément existe
            $relatedEntity = $entityManager->getRepository($entityClass)->findOneBy(['name' => $item]);

            // L'item n'existe pas
            if (!$relatedEntity){
                $relatedEntity = new $entityClass();
                $relatedEntity->setName($item);
                $entityManager->persist($relatedEntity);
            }

            // Ajout de l'entité au jeu
            $game->{$addMethod}($relatedEntity);
        }

        // Sauvegarde des changements
        $entityManager->flush();
    }
}
