<?php

namespace App\Controller\Admin;

use App\Classe\GameClass;
use App\Entity\Game;
use App\Entity\MysteryGame;
use App\Entity\Status;
use App\Entity\StreamMatch;
use App\Service\TranslatorService;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MysteryGameController extends AbstractController
{
    private Status $streamStatus;
    private Status $autoStatus;
    private Status $archivedStatus;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $client
    ) {
        $this->streamStatus = $this->entityManager->getRepository(Status::class)->findOneBy(['name' => 'stream']);
        $this->autoStatus = $this->entityManager->getRepository(Status::class)->findOneBy(['name' => 'auto']);
        $this->archivedStatus = $this->entityManager->getRepository(Status::class)->findOneBy(['name' => 'archived']);
    }

    #[Route('/admin-mystery-game-index', name: 'admin_mystery_game_index')]
    public function index()
    {
        $mysteryGame = $this->entityManager->getRepository(MysteryGame::class)->findOneBy(['status' => $this->streamStatus]);

        $streamMatch = $this->entityManager->getRepository(StreamMatch::class)->findOneBy(["id" => 1]);
        $streamMatchFormated = $this->formatGame($streamMatch);

        $searchHistory = $streamMatch->getSearchHistory();
        $searchHistory = $searchHistory ? json_decode($searchHistory, true) : [];

        return $this->render('admin/game/index.html.twig', [
            'mysteryGame' => $mysteryGame,
            'streamMatch' => $streamMatch,
            'streamMatchFormated' => $streamMatchFormated,
            'newHints' => [],
            'searchHistory' => $searchHistory
        ]);
    }

    #[Route('/admin-mystery-game-search', name: 'admin_mystery_game_search', methods: ['GET'])]
    public function search(Request $request): Response
    {
        $streamMatch = $this->entityManager->getRepository(StreamMatch::class)->findOneBy(["id" => 1]);
        $streamMatchFormated = $this->formatGame($streamMatch);

        $games = new GameClass($this->client);

        try {
            $results = $games->SearchGames($request);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des données.');
            return [];
        }

        return $this->render('admin/game/index.html.twig', [
            'mysteryGame' => true,
            'streamMatch' => $streamMatch,
            'streamMatchFormated' => $streamMatchFormated,
            'mysterySearchTerm' => $results['mysterySearchTerm'],
            'results' => $results['results'],
            'page' => $results['page'],
            'totalPages' => $results['totalPages'],
            'totalResults' => $results['totalResults']
        ]);
    }

    #[Route('/admin-mystery-game-find/{id}/{name}', name: 'admin_mystery_game_find', requirements: ['id' => '\d+', 'name' => '.+'], methods: ['GET'])]
    public function find(int $id, string $name, TranslatorService $translatorService): Response
    {
        $mysteryGame = $this->entityManager->getRepository(MysteryGame::class)->findOneBy(['status' => $this->streamStatus]);

        $gameClass = new GameClass($this->client);
        $game = $this->entityManager->getRepository(Game::class)->findOneBy(['gameId' => $id]);
        if (!$game) {
            $gameClass = new GameClass($this->client);
            $game = $gameClass->ShowGame($this->entityManager, $id, $name, $translatorService);
        }

        // if ($game->getName() == $mysteryGame->getName()) {
        //     dd('win');
        // }

        $result = $this->compareGame($mysteryGame, $game);
        $streamMatchFormated = $this->formatGame($result['streamMatch']);

        $this->entityManager->persist($result['streamMatch']);
        $this->entityManager->flush();

        return $this->render('admin/game/index.html.twig', [
            'mysteryGame' => $mysteryGame,
            'streamMatch' => $result['streamMatch'],
            'streamMatchFormated' => $streamMatchFormated,
            'newHints' => $result['newHints'],
            'searchHistory' => $result['searchHistory']
        ]);
    }

    #[Route('/admin-mystery-game-show', name: 'admin_mystery_game_show')]
    public function show(Request $request): Response
    {
        $data = $request->request->all();

        $mysteryGame = $this->setMysteryGame($data, $this->streamStatus);
        $streamMatch = $this->setStreamMatch($mysteryGame);

        $this->entityManager->persist($mysteryGame);
        $this->entityManager->persist($streamMatch);
        $this->entityManager->flush();

        $this->addFlash('success', 'Le jeu mystère a été ajouté au stream en cours');
        return $this->redirectToRoute('app_home');
    }

    #[Route('/admin-mystery-game-create', name: 'admin_mystery_game_create')]
    public function create(Request $request): Response
    {
        // Récupérer toutes les données du formulaire
        $data = $request->request->all();

        // Définir les groupes à traiter
        $groupPrefixes = [
            'artist_',
            'designer_',
            'developer_',
            'graphicDesigner_',
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
            'developers' => [],
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

    #[Route('/admin-push-auto', name: 'admin_push_auto')]
    public function pushToAuto(): Response
    {
        $mysteryGameAuto = $this->entityManager->getRepository(MysteryGame::class)->findOneBy(['status' => $this->autoStatus]);
        if ($mysteryGameAuto) {
            $mysteryGameAuto->setStatus($this->archivedStatus);
            $this->entityManager->persist($mysteryGameAuto);
        }
        $mysteryGame = $this->entityManager->getRepository(MysteryGame::class)->findOneBy(['status' => $this->streamStatus]);
        if ($mysteryGame) {
            $mysteryGame->setStatus($this->autoStatus);
            $this->entityManager->persist($mysteryGame);
        }
        $this->entityManager->flush();
        return $this->redirectToRoute('admin_game_index');
    }

    private function setMysteryGame(array $data, Status $status): MysteryGame
    {

        $players = explode('-', $data['players']);
        $minPlayers = (int)($players[0] ?? 1);
        $maxPlayers = (int)($players[1] ?? $minPlayers);

        $mysteryGame = new MysteryGame();
        $mysteryGame
            ->setCreatedAt(new DateTimeImmutable())
            ->setUpdatedAt($mysteryGame->getCreatedAt())
            ->setStatus($status)
            ->setName($data['name'])
            ->setYearPublished($data['yearPublished'])
            ->setMinPlayers($minPlayers)
            ->setMaxPlayers($maxPlayers)
            ->setPlayingTime($data['playingTime'])
            ->setAge($data['age']);

        $this->setOptionalJsonFields($mysteryGame, $data);

        return $mysteryGame;
    }

    private function setStreamMatch(MysteryGame $mysteryGame): StreamMatch
    {
        $fields = [
            'categories' => 'setCategoriesIndices',
            'subdomains' => 'setSubdomainsIndices',
            'mechanics' => 'setMechanicsIndices',
            'designers' => 'setDesignersIndices',
            'artists' => 'setArtistsIndices',
            'graphicDesigners' => 'setGraphicDesignersIndices',
            'honors' => 'setHonorsIndices',
            'publishers' => 'setPublishersIndices',
            'developers' => 'setDevelopersIndices'
        ];

        // Récupérer le StreamMatch existant ou en créer un nouveau
        $streamMatch = $this->entityManager->getRepository(StreamMatch::class)->findOneBy(['id' => 1]);
        if (!$streamMatch instanceof StreamMatch) {
            $streamMatch = new StreamMatch();
        }

        // Réinitialiser les valeurs fixes du StreamMatch
        $streamMatch
            ->setYearPublished("----")
            ->setMinPlayers("--")
            ->setMaxPlayers("--")
            ->setPlayingTime("--")
            ->setAge("--");

        // Boucle sur les champs dynamiques
        foreach ($fields as $property => $setter) {
            $values = explode(',', $mysteryGame->{'get' . ucfirst($property) . 'Indices'}());
            $placeholders = array_fill(0, count($values), "---");
            $streamMatch->$setter(json_encode($placeholders));
        }

        return $streamMatch;
    }


    private function setOptionalJsonFields(MysteryGame $mysteryGame, array $data): void
    {
        $fields = [
            'categories' => 'setCategoriesIndices',
            'subdomains' => 'setSubdomainsIndices',
            'mechanics' => 'setMechanicsIndices',
            'designers' => 'setDesignersIndices',
            'artists' => 'setArtistsIndices',
            'graphicDesigners' => 'setGraphicDesignersIndices',
            'honors' => 'setHonorsIndices',
            'publishers' => 'setPublishersIndices',
            'developers' => 'setDevelopersIndices'
        ];

        foreach ($fields as $key => $method) {
            if (isset($data[$key])) {
                $mysteryGame->$method($this->encodeJsonOrNull($data[$key] ?? null));
            }
        }
    }

    private function encodeJsonOrNull(?array $data): ?string
    {
        return empty($data) ? null : json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    private function formatGame($game): array
    {
        $gameFormatted = [];
        $gameFormatted['categoriesIndices'] = json_decode($game->getCategoriesIndices(), true);
        $gameFormatted['subdomainsIndices'] = json_decode($game->getSubdomainsIndices(), true);
        $gameFormatted['mechanicsIndices'] = json_decode($game->getMechanicsIndices(), true);
        $gameFormatted['designersIndices'] = json_decode($game->getDesignersIndices(), true);
        $gameFormatted['artistsIndices'] = json_decode($game->getArtistsIndices(), true);
        $gameFormatted['developersIndices'] = json_decode($game->getDevelopersIndices(), true);
        $gameFormatted['graphicDesignersIndices'] = json_decode($game->getGraphicDesignersIndices(), true);
        $gameFormatted['honorsIndices'] = json_decode($game->getHonorsIndices(), true);
        $gameFormatted['publishersIndices'] = json_decode($game->getPublishersIndices(), true);

        return $gameFormatted;
    }

    private function compareGame(MysteryGame $mysteryGame, Game $game): array
    {
        $name = $game->getName();
        $hintMatch = [];

        $streamMatch = $this->entityManager->getRepository(StreamMatch::class)->find(1);
        if (!$streamMatch) {
            throw new \Exception("Aucun StreamMatch trouvé.");
        }

        $newHints = [];

        // 🔹 Gestion de l'âge
        $currentAgeHint = $streamMatch->getAge();
        $mysteryAge = $mysteryGame->getAge();
        $proposedAge = $game->getAge();

        if ($mysteryAge != $currentAgeHint) {
            $newHint = $this->generateSimpleHint($currentAgeHint, $mysteryAge, $proposedAge);
            if ($currentAgeHint !== $newHint) {
                $streamMatch->setAge($newHint);
               $newHints['age'] = $newHint;
               $hintMatch['age'] = 'Age: ' .  $newHint . " ans";
            }
        } else {
            $hintMatch['age'] = 'Age: ' . $mysteryAge . " ans";
        }

        // 🔹 Gestion du temps de jeu (playingTime)
        $currentPlayingTimeHint = $streamMatch->getPlayingTime();
        $mysteryPlayingTime = $mysteryGame->getPlayingTime();
        $proposedPlayingTime = $game->getPlayingTime();

        if ($mysteryPlayingTime != $currentPlayingTimeHint) {
            $newHint = $this->generateSimpleHint($currentPlayingTimeHint, $mysteryPlayingTime, $proposedPlayingTime);
            if ($currentPlayingTimeHint !== $newHint) {
                $streamMatch->setPlayingTime($newHint);
                $newHints['playingTime'] = $newHint;
                $hintMatch['playingTime'] = 'Durée: ' .  $newHint . " mn";
            }
        } else {
            $hintMatch['playingTime'] = 'Durée: ' . $mysteryPlayingTime . " mn";
        }

        // 🔹 Gestion de la date de sortie (yearPublished)
        $currentYearPublishedHint = $streamMatch->getYearPublished();
        $mysteryYearPublished = $mysteryGame->getYearPublished();
        $proposedYearPublished = $game->getYearPublished();

        if ($mysteryYearPublished != $currentYearPublishedHint) {
            $newHint = $this->generateSimpleHint($currentYearPublishedHint, $mysteryYearPublished, $proposedYearPublished);
            if ($currentYearPublishedHint !== $newHint) {
                $streamMatch->setYearPublished($newHint);
                $hintMatch['yearPublished'] = 'Sortie: ' .  $newHints['yearPublished'] = $newHint;
            }
        } else {
            $hintMatch['yearPublished'] = $mysteryYearPublished;
        }

        // 🔹 Gestion du joueur minimum (minPlayers)
        $currentMinPlayers = $streamMatch->getMinPlayers();
        $mysteryMinPlayers = $mysteryGame->getMinPlayers();
        $proposedMinPlayers = $game->getMinPlayers();

        if ($mysteryMinPlayers != $currentMinPlayers) {
            $newHint = $this->generateSimpleHint($currentMinPlayers, $mysteryMinPlayers, $proposedMinPlayers);
            if ($currentMinPlayers !== $newHint) {
                $streamMatch->setMinPlayers($newHint);
                $newHints['minPlayers'] = $newHint;
                $hintMatch['minPlayers'] = 'Min: ' .  $newHint . 'j';
            }
        } else {
            $hintMatch['minPlayers'] = 'Min: ' . $mysteryMinPlayers . 'j';
        }

        // 🔹 Gestion du joueur minimum (minPlayers)
        $currentMaxPlayers = $streamMatch->getMaxPlayers();
        $mysteryMaxPlayers = $mysteryGame->getMaxPlayers();
        $proposedMaxPlayers = $game->getMaxPlayers();

        if ($mysteryMaxPlayers != $currentMaxPlayers) {
            $newHint = $this->generateSimpleHint($currentMaxPlayers, $mysteryMaxPlayers, $proposedMaxPlayers);
            if ($currentMaxPlayers !== $newHint) {
                $streamMatch->setMaxPlayers($newHint);
                $newHints['maxPlayers'] = $newHint;
                $hintMatch['maxPlayers'] = 'Max: ' . $newHint . 'j';
            }
        } else {
            $hintMatch['maxPlayers'] = 'Max: ' . $mysteryMaxPlayers . 'j';
        }

        // 🔹 Gestion des Thèmes (categories)
        $currentCategories = json_decode($streamMatch->getCategoriesIndices(), true);
        $mysteryCategories = json_decode($mysteryGame->getCategoriesIndices(), true);
        $proposedCategories = $game->getCategories();

        if ($mysteryCategories != $currentCategories) {
            foreach ($proposedCategories as $proposedCategory) {
                $translatedName = $proposedCategory->getTranslatedName();

                foreach ($mysteryCategories as $index => $mysteryCategory) {
                    if ($mysteryCategory !== $currentCategories[$index]) {
                        if ($mysteryCategory === $translatedName) {
                            $currentCategories[$index] = $mysteryCategory;
                            $hintMatch['categories'] = 'Thèmes: ' . $newHints['categories'] = $mysteryCategory;
                        }
                    }
                }
            }
            $streamMatch->setCategoriesIndices(json_encode($currentCategories));
        } else {
            $hintMatch['categories'] = 'Thèmes: ' . implode(', ', $mysteryCategories);
        }

        // 🔹 Gestion des catégories (subdomains)
        $currentSubdomains = json_decode($streamMatch->getSubdomainsIndices(), true);
        $mysterySubdomains = json_decode($mysteryGame->getSubdomainsIndices(), true);
        $proposedSubdomains = $game->getSubdomains();

        if ($mysterySubdomains != $currentSubdomains) {
            foreach ($proposedSubdomains as $proposedSubdomain) {
                $translatedName = $proposedSubdomain->getTranslatedName();

                foreach ($mysterySubdomains as $index => $mysterySubdomain) {
                    if ($mysterySubdomain !== $currentSubdomains[$index]) {
                        if ($mysterySubdomain === $translatedName) {
                            $currentSubdomains[$index] = $mysterySubdomain;
                            $hintMatch['subdomains'] = 'Catégories: ' . $newHints['subdomains'] = $mysterySubdomain;
                        }
                    }
                }
            }
            $streamMatch->setSubdomainsIndices(json_encode($currentSubdomains));
        } else {
            $hintMatch['subdomains'] = 'Catégories: ' . implode(', ', $mysterySubdomains);
        }

        // 🔹 Gestion des mécanisme (mechanics)
        $currentMechanics = json_decode($streamMatch->getMechanicsIndices(), true);
        $mysteryMechanics = json_decode($mysteryGame->getMechanicsIndices(), true);
        $proposedMechanics = $game->getMechanics();

        if ($mysteryMechanics != $currentMechanics) {
            foreach ($proposedMechanics as $proposedMechanic) {
                $translatedName = $proposedMechanic->getTranslatedName();

                foreach ($mysteryMechanics as $index => $mysteryMechanic) {
                    if ($mysteryMechanic !== $currentMechanics[$index]) {
                        if ($mysteryMechanic === $translatedName) {
                            $currentMechanics[$index] = $mysteryMechanic;
                            $hintMatch['mechanics'] = 'Mécanismes: ' . $newHints['mechanics'] = $mysteryMechanic;
                        }
                    }
                }
            }
            $streamMatch->setMechanicsIndices(json_encode($currentMechanics));
        } else {
            $hintMatch['mechanics'] = 'Mécanismes: ' . implode(', ', $mysteryMechanics);
        }

        // 🔹 Gestion des créateurs (designers)
        $currentDesigners = json_decode($streamMatch->getDesignersIndices(), true);
        $mysteryDesigners = json_decode($mysteryGame->getDesignersIndices(), true);
        $proposedDesigners = $game->getDesigners();

        if ($mysteryDesigners != $currentDesigners) {
            $result = $this->generateComplexHint($currentDesigners, $mysteryDesigners, $proposedDesigners);
            $streamMatch->setDesignersIndices(json_encode($result['currentHints']));
            if ($result['find']) {
               $newHints['designers'] = $result['currentHints'];
                $hintMatch['designers'] = 'Créateurs: ' . implode(', ', $result['currentHints']);
            }
        } else {
            $hintMatch['designers'] = 'Créateurs: ' . implode(', ', $mysteryDesigners);
        }

        // 🔹 Gestion des Illustrateurs (artists)
        $currentArtists = json_decode($streamMatch->getArtistsIndices(), true);
        $mysteryArtists = json_decode($mysteryGame->getArtistsIndices(), true);
        $proposedArtists = $game->getArtists();

        if ($mysteryArtists != $currentArtists) {
            $result = $this->generateComplexHint($currentArtists, $mysteryArtists, $proposedArtists);
            $streamMatch->setArtistsIndices(json_encode($result['currentHints']));
            if ($result['find']) {
                $newHints['artists'] = $result['currentHints'];
                $hintMatch['artists'] = 'Illustrateurs: ' . implode(', ', $result['currentHints']);
            }
        } else {
            $hintMatch['artists'] = 'Illustrateurs' . implode(', ', $mysteryArtists);
        }

        // 🔹 Gestion des Développeurs (developers)
        $currentDevelopers = json_decode($streamMatch->getDevelopersIndices(), true);
        $mysteryDevelopers = json_decode($mysteryGame->getDevelopersIndices(), true);
        $proposedDevelopers = $game->getDevelopers();

        if ($mysteryDevelopers != $currentDevelopers) {
            $result = $this->generateComplexHint($currentDevelopers, $mysteryDevelopers, $proposedDevelopers);
            $streamMatch->setDevelopersIndices(json_encode($result['currentHints']));
            if ($result['find']) {
                $newHints['developers'] = $result['currentHints'];
                $hintMatch['developers'] = 'Développeurs: ' . implode(', ', $result['currentHints']);
            }
        } else {
            $hintMatch['developers'] = 'Développeurs' . implode(', ', $mysteryDevelopers);
        }

        // 🔹 Gestion des Designers (graphicDesigners)
        $currentGraphicDesigners = json_decode($streamMatch->getGraphicDesignersIndices(), true);
        $mysteryGraphicDesigners = json_decode($mysteryGame->getGraphicDesignersIndices(), true);
        $proposedGraphicDesigners = $game->getGraphicDesigners();

        if ($mysteryGraphicDesigners != $currentGraphicDesigners) {
            $result = $this->generateComplexHint($currentGraphicDesigners, $mysteryGraphicDesigners, $proposedGraphicDesigners);
            $streamMatch->setGraphicDesignersIndices(json_encode($result['currentHints']));
            if ($result['find']) {
                $newHints['graphicDesigners'] = $result['currentHints'];
                $hintMatch['graphicDesigners'] = 'Designers: ' . implode(', ', $result['currentHints']);
            }
        } else {
            $hintMatch['graphicDesigners'] = 'Designers: ' . implode(', ', $mysteryGraphicDesigners);
        }

        // 🔹 Gestion des Editeurs (publishers)
        $currentPublishers = json_decode($streamMatch->getPublishersIndices(), true);
        $mysteryPublishers = json_decode($mysteryGame->getPublishersIndices(), true);
        $proposedPublishers = $game->getPublishers();

        if ($mysteryPublishers != $currentPublishers) {
            $result = $this->generateComplexHint($currentPublishers, $mysteryPublishers, $proposedPublishers);
            $streamMatch->setPublishersIndices(json_encode($result['currentHints']));
            if ($result['find']) {
                $newHints['publishers'] = $result['currentHints'];
                $hintMatch['publishers'] = 'Editeurs: ' . implode(', ', $result['currentHints']);
            }
        } else {
            $hintMatch['publishers'] = 'Editeurs: ' . implode(', ', $mysteryPublishers);
        }

        // 🔹 Gestion des Récompenses (honors)
        $currentHonors = json_decode($streamMatch->getHonorsIndices(), true);
        $mysteryHonors = json_decode($mysteryGame->getHonorsIndices(), true);
        $proposedHonors = $game->getHonorGames();

        if ($mysteryHonors != $currentHonors) {
            foreach ($proposedHonors as $proposedHonor) {
                foreach ($mysteryHonors as $index => $mysteryHonor) {
                    if ($mysteryHonor !== $currentHonors[$index]) {
                        if ($mysteryHonor === $proposedHonor->getHonor()->getName()) {
                            $currentHonors[$index] = $mysteryHonor;
                            $hintMatch['honors'] = 'Récompenses: ' . $newHints['honors'] = $mysteryHonor;
                        }
                    }
                }
            }
            $streamMatch->setHonorsIndices(json_encode($currentHonors));
        } else {
            $hintMatch['honors'] = 'Récompenses: ' . implode(', ', $mysteryHonors);
        }

        $searchHistory = $streamMatch->getSearchHistory();

        $searchHistory = $searchHistory ? json_decode($searchHistory, true) : [];

        $alreadyExists = false;
        foreach ($searchHistory as $entry) {
            if ($entry['name'] === $name) {
                $alreadyExists = true;
                break;
            }
        }

        if (!$alreadyExists) {
            $searchHistory[] = [
                "name" => $name,
                "hintMatch" => $hintMatch
            ];
            $streamMatch->setSearchHistory(json_encode($searchHistory, JSON_UNESCAPED_UNICODE));
        }

        $searchHistory = $streamMatch->getSearchHistory();
        $searchHistory = $searchHistory ? json_decode($searchHistory, true) : [];

        return compact('streamMatch', 'newHints', 'searchHistory');
    }


    /**
     * Génère un indice basé sur une valeur mystère et une proposition utilisateur.
     */
    private function generateSimpleHint(string $currentHint, int $mysteryValue, int $proposedValue): string
    {
        // ✅ Si la bonne réponse est trouvée, on efface les autres indices
        if ($mysteryValue == $proposedValue) {
            return (string) $mysteryValue;
        }

        // ✅ Extraction des bornes actuelles
        preg_match('/> (\d+)/', $currentHint, $minMatch);
        preg_match('/< (\d+)/', $currentHint, $maxMatch);

        $currentMin = $minMatch[1] ?? null;
        $currentMax = $maxMatch[1] ?? null;

        // ✅ Mise à jour des bornes sans écraser les indices existants
        $currentMin = ($mysteryValue > $proposedValue && ($currentMin === null || $proposedValue > $currentMin)) ? $proposedValue : $currentMin;
        $currentMax = ($mysteryValue < $proposedValue && ($currentMax === null || $proposedValue < $currentMax)) ? $proposedValue : $currentMax;

        // ✅ Construction du nouvel indice avec priorité à ">" puis "<"
        return ($currentMin !== null ? "> $currentMin" : "") .
            ($currentMax !== null ? ($currentMin !== null ? " et " : "") . "< $currentMax" : "");
    }

    private function generateComplexHint(array $currentHints, array $mysteryHints, Collection $proposeHints): array
    {
        $find = false;
        foreach ($proposeHints as $proposeHint) {
            foreach ($mysteryHints as $index => $mysteryHint) {
                if ($mysteryHint !== $currentHints[$index]) {
                    if ($mysteryHint === $proposeHint->getName()) {
                        $currentHints[$index] = $mysteryHint;
                        $find = true;
                    }
                }
            }
        }

        return [
            'currentHints' => $currentHints,
            'find' => $find
        ];
    }
}
