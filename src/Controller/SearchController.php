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

        try {
            $response = $this->client->request('GET', $url);
            $content = $response->getContent();

            // Charger et analyser le XML correctement
            $xml = new \SimpleXMLElement($content);
            $results = [];

            // Parcourir chaque élément <boardgame> du XML
            foreach ($xml->boardgame as $game) {
                $name = (string) $game->name;
                $yearPublished = (string) $game->yearpublished;
                $id = (string) $game['objectid'];

                $results[] = [
                    'id' => $id,
                    'name' => $name,
                    'year' => $yearPublished ?: 'N/A'
                ];
            }

            return $results;
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des données.');
            return [];
        }
    }
}
