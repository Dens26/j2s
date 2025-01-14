<?php

namespace App\Controller\navbar;

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
    public function show(int $id, string $name, Request $request): Response
    {
        $url = "https://boardgamegeek.com/xmlapi/boardgame/$id";

        $response = $this->client->request('GET', $url);
        $content = $response->getContent();
        $xml = new \SimpleXMLElement($content);

        $results = [
            'name' => (string)$name,
            'names' => $xml->boardgame->name ? (array)$xml->boardgame->name : [],
            'yearPublished' => (string)$xml->boardgame->yearpublished ?? null,
            'minPlayers' => (string)$xml->boardgame->minplayers ?? null,
            'maxPlayers' => (string)$xml->boardgame->maxplayers ?? null,
            'playingTime' => (string)$xml->boardgame->playingtime ?? null,
            'minPlayTime' => (string)$xml->boardgame->minplaytime ?? null,
            'maxPlayTime' => (string)$xml->boardgame->maxplaytime ?? null,
            'age' => (string)$xml->boardgame->age ?? null,
            'description' => (string)$xml->boardgame->description ?? null,
            'thumbnail' => (string)$xml->boardgame->thumbnail ?? null,
            'image' => (string)$xml->boardgame->image ?? null,
            'publishers' => $xml->boardgame->boardgamepublisher ? (array)$xml->boardgame->boardgamepublisher : [],
            'versions' => $xml->boardgame->boardgameversion ? (array)$xml->boardgame->boardgameversion : [],
            'artists' => $xml->boardgame->boardgameartist ? (array)$xml->boardgame->boardgameartist : [],
            'categories' => $xml->boardgame->boardgamecategory ? (array)$xml->boardgame->boardgamecategory : [],
            'subdomain' => $xml->boardgame->boardgamesubdomain ? (array)$xml->boardgame->boardgamesubdomain : [],
            'mechanic' => $xml->boardgame->boardgamemechanic ? (array)$xml->boardgame->boardgamemechanic : [],
            'designer' => $xml->boardgame->boardgamedesigner ? (array)$xml->boardgame->boardgamedesigner : [],
            'graphicDesigner' => $xml->boardgame->boardgamegraphicdesigner ? (array)$xml->boardgame->boardgamegraphicdesigner : [],
            'developer' => $xml->boardgame->boardgamedeveloper ? (array)$xml->boardgame->boardgamedeveloper : [],
            'honor' => $xml->boardgame->boardgamehonor ? (array)$xml->boardgame->boardgamehonor : [],
            'family' => $xml->boardgame->boardgamefamily ? (array)$xml->boardgame->boardgamefamily : [],
        ];
        /*
            Translate description with DeepL API Free
        */

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
}
