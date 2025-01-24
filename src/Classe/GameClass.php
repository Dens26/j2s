<?php

namespace App\Classe;

use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\Designer;
use App\Entity\Developer;
use App\Entity\Family;
use App\Entity\Game;
use App\Entity\GraphicDesigner;
use App\Entity\Honor;
use App\Entity\HonorGame;
use App\Entity\Mechanic;
use App\Entity\Publisher;
use App\Entity\Subdomain;
use App\Service\TranslatorService;
use Doctrine\ORM\EntityManagerInterface;
use HTMLPurifier;
use HTMLPurifier_Config;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GameClass
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function SearchGames(Request $request): array
    {
        $searchTerm = $request->query->get('search', '');
        $page = (int)$request->query->get('page', 1);
        $resultsPerPage = 10;

        $data = $this->fetchBoardGameGeekData($searchTerm, $page, $resultsPerPage);
        $results = $data['results'];
        $totalPages = $data['totalPages'];
        $totalResults = $data['totalResults'];

        return [
            'searchTerm' => $searchTerm,
            'results' => $results,
            'page' => $page,
            'totalPages' => $totalPages,
            'totalResults' => $totalResults
        ];
    }

    public function ShowGame(EntityManagerInterface $entityManager, int $id, string $name, TranslatorService $translatorService): Game
    {
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,strong,em,br,ul,ol,li,a,code,pre,blockquote');
        $purifier = new HTMLPurifier($config);

        // Game not exist : Call the API
        $url = "https://boardgamegeek.com/xmlapi/boardgame/$id";
        $response = $this->client->request('GET', $url);
        $content = $response->getContent();
        $xml = new \SimpleXMLElement($content);

        // Mapping Game data
        $results = [
            'name' => (string)$name,
            'names' => $xml->boardgame->name ? json_encode($this->filterNames((array)$xml->boardgame->name)) : '[]',
            'yearPublished' => (string)$xml->boardgame->yearpublished ?? null,
            'minPlayers' => (string)$xml->boardgame->minplayers ?? null,
            'maxPlayers' => (string)$xml->boardgame->maxplayers ?? null,
            'playingTime' => (string)$xml->boardgame->playingtime ?? null,
            // 'minPlayTime' => (string)$xml->boardgame->minplaytime ?? null,
            // 'maxPlayTime' => (string)$xml->boardgame->maxplaytime ?? null,
            'age' => (string)$xml->boardgame->age ?? null,
            'description' => $purifier->purify((string)$xml->boardgame->description . "<script>alert('Test de script');</script>" ?? null),
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
            'honors' => $xml->boardgame->boardgamehonor ? (array)$xml->boardgame->boardgamehonor : [],
            'families' => $xml->boardgame->boardgamefamily ? (array)$xml->boardgame->boardgamefamily : [],
        ];

        // Si il n'y a pas assez de credit pour traduire la description
        $translateAvailable = $translatorService->checkIfQuotaAvailable($results['description']);

        // Traduction des autres tables
        $this->translateItems($results, $translateAvailable, $translatorService);

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

        if ($translateAvailable) {
            $entityManager->persist($game);
        }

        // Gestion des table liées
        $this->handleRelateData(false, $results['publishers'], $game, Publisher::class, 'addPublisher', $entityManager, $translateAvailable);
        $this->handleRelateData(false, $results['artists'], $game, Artist::class, 'addArtist', $entityManager, $translateAvailable);
        $this->handleRelateData(true, $results['categories'], $game, Category::class, 'addCategory', $entityManager, $translateAvailable);
        $this->handleRelateData(true, $results['subdomains'], $game, Subdomain::class, 'addSubdomain', $entityManager, $translateAvailable);
        $this->handleRelateData(true, $results['mechanics'], $game, Mechanic::class, 'addMechanic', $entityManager, $translateAvailable);
        $this->handleRelateData(false, $results['designers'], $game, Designer::class, 'addDesigner', $entityManager, $translateAvailable);
        $this->handleRelateData(false, $results['graphicDesigners'], $game, GraphicDesigner::class, 'addGraphicDesigner', $entityManager, $translateAvailable);
        $this->handleRelateData(false, $results['developers'], $game, Developer::class, 'addDeveloper', $entityManager, $translateAvailable);
        $this->handleRelateHonorData($results['honors'], $game, $entityManager, $translateAvailable);
        $this->handleRelateData(true, $results['families'], $game, Family::class, 'addFamily', $entityManager, $translateAvailable);

        // Sauvegarde des données
        if ($translateAvailable) {
            $entityManager->flush();
        }

        // Rafraîchir l'entité pour garantir que les relations sont chargées
        if ($translateAvailable) {
            $entityManager->refresh($game);
        }

        return $game;
    }

    public function formatGame(Game $game): array
    {
        $results = [
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
            'categories' => $game->getCategories()->map(fn($category) => $category->getTranslatedName())->toArray(),
            'subdomains' => $game->getSubdomains()->map(fn($subdomain) => $subdomain->getTranslatedName())->toArray(),
            'mechanics' => $game->getMechanics()->map(fn($mechanic) => $mechanic->getTranslatedName())->toArray(),
            'designers' => $game->getDesigners()->map(fn($designer) => $designer->getName())->toArray(),
            'graphicDesigners' => $game->getGraphicDesigners()->map(fn($graphicDesigner) => $graphicDesigner->getName())->toArray(),
            'developers' => $game->getDevelopers()->map(fn($developer) => $developer->getName())->toArray(),
            'honors' => $game->getHonorGames()->map(function ($honorGame) {
                return [
                    'name' => $honorGame->getHonor()->getName(),
                    'year' => $honorGame->getYear(),
                ];
            })->toArray(),
            'families' => $game->getFamilies()->map(fn($family) => $family->getTranslatedName())->toArray(),
        ];
        // Trier les honors par année
        usort($results['honors'], function ($a, $b) {
            return $b['year'] <=> $a['year'];
        });
        return $results;
    }

    private function translateItems(array &$results, bool $translateAvailable, TranslatorService $translatorService): void
    {
        // Liste des champs à traduire (en plus de la description)
        $fieldsToTranslate = ['categories', 'subdomains', 'mechanics', 'families'];

        // Créer un tableau des valeurs à traduire (en incluant la description)
        $itemsToTranslate = [];

        // Ajout des catégories, sous-domaines, mécaniques, familles
        foreach ($fieldsToTranslate as $field) {
            if (isset($results[$field]) && is_array($results[$field]) && count($results[$field]) > 1) {
                $itemsToTranslate = array_merge($itemsToTranslate, array_slice($results[$field], 1));
            }
        }

        // Ajouter la description dans le tableau à traduire
        if (isset($results['description'])) {
            $itemsToTranslate[] = $results['description'];
        }

        // Si la traduction est disponible et qu'il y a des éléments à traduire, procéder à la traduction
        if ($translateAvailable && !empty($itemsToTranslate)) {
            $translatedItems = json_decode($translatorService->translate(json_encode($itemsToTranslate)), true);

            if (is_array($translatedItems)) {
                $index = 0;

                // Réintégrer les traductions dans les résultats
                foreach ($fieldsToTranslate as $field) {
                    if (isset($results[$field]) && is_array($results[$field]) && count($results[$field]) > 1) {
                        // Traduction des autres champs (categories, subdomains, etc.)
                        $results[$field] = array_map(function ($original) use (&$index, $translatedItems) {
                            return [
                                'name' => $original,
                                'translated-name' => $translatedItems[$index++] ?? 'Pas de traduction',
                            ];
                        }, array_slice($results[$field], 1));
                    }
                }

                // Mettre à jour la description avec le texte traduit
                if (isset($results['description'])) {
                    $results['description'] = $translatedItems[$index] ?? 'Pas de traduction';
                }
            } else {
                // Si la traduction échoue, utiliser l'original ou une valeur par défaut
                $this->setDefaultTranslations($results, $fieldsToTranslate);
            }
        } else {
            // Si la traduction n'est pas disponible, ajouter des traductions par défaut
            $this->setDefaultTranslations($results, $fieldsToTranslate);
        }
    }

    private function setDefaultTranslations(array &$results, array $fieldsToTranslate): void
    {
        // Si la traduction échoue ou n'est pas disponible, définir des traductions par défaut
        foreach ($fieldsToTranslate as $field) {
            if ($field === 'description' && isset($results['description'])) {
                $results['description'] = 'Pas de traduction';  // Utilisation d'une valeur par défaut pour la description
            } elseif (isset($results[$field]) && is_array($results[$field]) && count($results[$field]) > 1) {
                $results[$field] = array_map(function ($original) {
                    return [
                        'name' => $original,
                        'translated-name' => 'Pas de traduction',
                    ];
                }, array_slice($results[$field], 1));
            }
        }
    }



    private function fetchBoardGameGeekData(string $searchTerm, int $page, int $resultsPerPage): array
    {
        $url = 'https://boardgamegeek.com/xmlapi/search?search=' . urlencode($searchTerm);
        $results = [];
        $totalResults = 0;

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

    private function filterNames(array $data): array
    {
        return array_filter(
            array_map(
                fn($item) => is_object($item) ? (string)$item : $item,
                $data
            ),
            fn($item) => is_string($item) && $this->isLatinString($item)
        );
    }

    private function isLatinString(string $string): bool
    {
        // Vérifie si la chaîne contient uniquement des caractères latins, espaces ou ponctuations simples
        return preg_match('/^[a-zA-Z0-9\s\p{P}]+$/u', $string);
    }

    private function handleRelateData(bool $translate, array $data, Game $game, string $entityClass, string $addMethod, EntityManagerInterface $entityManager, bool $translateAvailable): void
    {
        if ($translate) {
            foreach ($data as $item) {
                // Vérification si l'élément existe (en utilisant 'name' pour les données traduites)
                $relatedEntity = $entityManager->getRepository($entityClass)->findOneBy(['name' => $item['name']]);

                // L'item n'existe pas
                if (!$relatedEntity) {
                    $relatedEntity = new $entityClass();
                    $relatedEntity->setName($item['name']);
                    $relatedEntity->setTranslatedName($item['translated-name']); // Enregistrer la traduction aussi

                    if ($translateAvailable) {
                        $entityManager->persist($relatedEntity);
                    }
                }

                // Ajout de l'entité au jeu pour chaque élément traduit
                $game->{$addMethod}($relatedEntity);
            }
        } else {
            // Filtrage des données pour ne garder que les valeurs de type string
            $filteredData = array_filter($data, fn($item) => is_string($item));

            foreach ($filteredData as $item) {
                // Vérification si l'élément existe (en utilisant 'name' pour les données non traduites)
                $relatedEntity = $entityManager->getRepository($entityClass)->findOneBy(['name' => $item]);

                // L'item n'existe pas
                if (!$relatedEntity) {
                    $relatedEntity = new $entityClass();
                    $relatedEntity->setName($item);

                    if ($translateAvailable) {
                        $entityManager->persist($relatedEntity);
                    }
                }

                // Ajout de l'entité au jeu pour chaque élément non traduit
                $game->{$addMethod}($relatedEntity);
            }
        }
    }

    private function handleRelateHonorData(array $data, Game $game, EntityManagerInterface $entityManager, bool $translateAvailable): void
    {
        // Filtrage des données pour ne garder que les valeurs de type string
        $filteredData = array_filter($data, fn($item) => is_string($item));
        foreach ($filteredData as $item) {
            // Vérification si l'élément existe
            $year = substr($item, 0, 4);
            $name = substr($item, 5);
            $honor = $entityManager->getRepository(Honor::class)->findOneBy(['name' => $name]);

            // L'item n'existe pas
            if (!$honor) {
                $honor = new Honor();
                $honor->setName($name);
                if ($translateAvailable) {
                    $entityManager->persist($honor);
                }
            }

            $honorGame = new HonorGame();
            $honorGame->setGame($game);
            $honorGame->setHonor($honor);
            $honorGame->setYear($year);

            if ($translateAvailable) {
                $entityManager->persist($honorGame);
            }
        }
        // Flusher toutes les entités persistées
        if ($translateAvailable) {
            $entityManager->flush();
        }
    }
}
