<?php

namespace App\Controller;

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

    #[Route('/search', name: 'app_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $searchTerm = $request->query->get('search', '');
        $results = [];

        if ($searchTerm) {
            $results = $this->fetchBoardGameGeekData($searchTerm);
        }

        return $this->render('base.html.twig', [
            'searchTerm' => $searchTerm,
            'results' => $results
        ]);
    }

    private function fetchBoardGameGeekData(string $searchTerm): array
    {
        $url = 'https://boardgamegeek.com/xmlapi/search?search=' . urlencode($searchTerm);
        $results = [];

        try {
            $response = $this->client->request('GET', $url);
            $content = $response->getContent();
            $xml = new \SimpleXMLElement($content);

            // Parcourir chaque boardgame trouvé
            foreach ($xml->boardgame as $game) {
                $id = (string) $game['objectid'];
                $name = (string) $game->name;
                $yearPublished = (string) $game->yearpublished ?: 'N/A';

                // Appel API pour récupérer le thumbnail
                $thumbnail = $this->fetchThumbnail($id);

                $results[] = [
                    'id' => $id,
                    'name' => $name,
                    'year' => $yearPublished,
                    'thumbnail' => $thumbnail
                ];
            }

            return $results;
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
