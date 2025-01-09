<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class BoardgameController extends AbstractController
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    #[Route('/search', name: 'app_boardgame_search', methods: ['GET'])]
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
                        'year' => $yearPublished,
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
