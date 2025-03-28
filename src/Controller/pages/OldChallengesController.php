<?php

namespace App\Controller\pages;

use App\Classe\GameClass;
use App\Entity\Game;
use App\Entity\GameScore;
use App\Entity\MysteryGame;
use App\Entity\Status;
use App\Service\TranslatorService;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OldChallengesController extends AbstractController
{
    private Status $archivedStatus;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private HttpClientInterface $client
    ) {
        $this->archivedStatus = $this->entityManager->getRepository(Status::class)->findOneBy(['name' => 'archived']);
    }

    #[Route('/oldchallenges', name: 'app_oldchallenges')]
    public function index(): Response
    {
        $mysteryGames = $this->entityManager->getRepository(MysteryGame::class)->findBy(
            ['status' => $this->archivedStatus],
            ['createdAt' => 'DESC']
        );

        // Groupement par mois
        $monthsFr = [
            1 => 'Janvier',
            2 => 'Février',
            3 => 'Mars',
            4 => 'Avril',
            5 => 'Mai',
            6 => 'Juin',
            7 => 'Juillet',
            8 => 'Août',
            9 => 'Septembre',
            10 => 'Octobre',
            11 => 'Novembre',
            12 => 'Décembre',
        ];

        $groupedMysteryGames = [];
        foreach ($mysteryGames as $mysteryGame) {
            $gameScore = $this->entityManager->getRepository(GameScore::class)->findOneBy([
                'mysteryGame' => $mysteryGame->getId(),
                'User' => $this->getUser()
            ]);
            $score = $gameScore ? $gameScore->getScore() : null;
            $progression = $gameScore ? $gameScore->getProgression() : 0;
            $nbrOfIndices = $gameScore ? $gameScore->getNbrOfIndices() : null;

            $mysteryGameData = [
                'game' => $mysteryGame,
                'score' => $score,
                'progression' => $progression,
                'nbrOfIndices' => $nbrOfIndices
            ];

            $date = $mysteryGame->getUpdatedAt();
            $monthKey = $date->format('Y-m');
            $monthLabel = $monthsFr[(int) $date->format('n')] . ' ' . $date->format('Y');

            if (!isset($groupedMysteryGames[$monthKey])) {
                $groupedMysteryGames[$monthKey] = [
                    'label' => $monthLabel,
                    'games' => [],
                ];
            }

            $groupedMysteryGames[$monthKey]['games'][] = $mysteryGameData;
        }

        return $this->render('pages/old_challenges/index.html.twig', [
            'groupedMysteryGames' => $groupedMysteryGames,
        ]);
    }

    #[Route('/oldchallenges-play', name: 'app_oldchallenges_play')]
    public function play(Request $request)
    {
        $id = $request->query->get('id');

        $mysteryGame = $this->entityManager->getRepository(MysteryGame::class)->findOneBy(['id' => $id]);
        $gameScore = $this->entityManager->getRepository(GameScore::class)->findOneBy([
            'mysteryGame' => $id,
            'User' => $this->getUser()
        ]);

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

        if (!$mysteryGame) {
            return $this->redirectToRoute('app_home');
        }

        $isWin = false;

        $nbrOfIndices = 5;
        if (!$gameScore) {
            /** @var GameScore $gameScore */
            $gameScore = new GameScore();
            $gameScore
                ->setUser($this->getUser())
                ->setMysteryGame($mysteryGame)
                ->setYearPublished('----')
                ->setMinPlayers('--')
                ->setMaxPlayers('--')
                ->setPlayingTime('--')
                ->setAge('--')
                ->setAttempt(0);

            // Boucle sur les champs dynamiques
            foreach ($fields as $property => $setter) {
                $values = explode(',', $mysteryGame->{'get' . ucfirst($property) . 'Indices'}());
                $placeholders = array_fill(0, count($values), "---");
                $gameScore->$setter(json_encode($placeholders));

                $nbrOfIndices += count($values);
            }
            $gameScore->setNbrOfIndices($nbrOfIndices);
            $gameScore->setSearchHistory('');

            $this->entityManager->persist($gameScore);
            $this->entityManager->flush();
        } else if ($gameScore->getScore() != null) {
            $isWin = true;
        }
        $gameScoreFormated = $this->formatGame($gameScore);

        $searchHistory = $this->formatSearchHistory($gameScore);

        return $this->render('pages/old_challenges/play.html.twig', [
            'mysteryGame' => $mysteryGame,
            'gameScore' => $gameScore,
            'gameScoreFormated' => $gameScoreFormated,
            'isWin' => $isWin,
            'searchHistory' => $searchHistory
        ]);
    }



    #[Route('/app-oldchallenges-search', name: 'app_oldchallenges_search', methods: ['GET'])]
    public function search(Request $request, SluggerInterface $slugger): Response
    {
        // $search = $request->query->get('search');
        $id = $request->query->get('id');

        $mysteryGame = $this->entityManager->getRepository(MysteryGame::class)->findOneBy(['id' => $id]);

        $gameScore = $this->entityManager->getRepository(GameScore::class)->findOneBy([
            'mysteryGame' => $mysteryGame->getId(),
            'User' => $this->getUser()
        ]);
        $gameScoreFormated = $this->formatGame($gameScore);
        if ($gameScore->getScore() != null) {
            return $this->render('pages/old_challenges/play.html.twig', [
                'mysteryGame' => $mysteryGame,
                'gameScore' => $gameScore,
                'gameScoreFormated' => $gameScoreFormated,
                'isWin' => true,
                'searchHistory' => null
            ]);
        }

        $games = new GameClass($this->client);

        try {
            $results = $games->SearchGames($request, $slugger);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Erreur lors de la récupération des données.');
            return $this->redirectToRoute('app_oldchallenges');
        }

        return $this->render('pages/old_challenges/play.html.twig', [
            'mysteryGame' => $mysteryGame,
            'gameScore' => $gameScore,
            'gameScoreFormated' => $gameScoreFormated,
            'oldChallengesSearchTerm' => $results['oldChallengesSearchTerm'],
            'results' => $results['results'],
            'page' => $results['page'],
            'totalPages' => $results['totalPages'],
            'totalResults' => $results['totalResults'],
            'searchHistory' => null,
            'isWin' => false
        ]);
    }

    #[Route('/app-oldchallenges-find', name: 'app_oldchallenges_find')]
    public function find(Request $request, TranslatorService $translatorService): Response
    {
        $mysteryGameId = $request->query->get('mysteryGameId');
        $id = $request->query->get('id');
        $name = $request->query->get('name');

        $mysteryGame = $this->entityManager->getRepository(MysteryGame::class)->findOneBy(['id' => $mysteryGameId]);

        $gameScore = $this->entityManager->getRepository(GameScore::class)->findOneBy([
            'mysteryGame' => $mysteryGame->getId(),
            'User' => $this->getUser()
        ]);
        $gameScoreFormated = $this->formatGame($gameScore);
        if ($gameScore->getScore() != null) {
            return $this->render('pages/find_the_game/index.html.twig', [
                'mysteryGame' => $mysteryGame,
                'gameScore' => $gameScore,
                'gameScoreFormated' => $gameScoreFormated,
                'isWin' => true,
                'searchHistory' => null
            ]);
        }

        $gameClass = new GameClass($this->client);
        $game = $this->entityManager->getRepository(Game::class)->findOneBy(['gameId' => $id]);
        if (!$game) {
            $gameClass = new GameClass($this->client);
            $game = $gameClass->ShowGame($this->entityManager, $id, $name, $translatorService);
        }

        $result = $this->compareGame($game, $gameScore, $mysteryGame);
        $attempt = $gameScore->getAttempt();
        $progression = $gameScore->getProgression();

        $gameScoreFormated = $this->formatGame($gameScore);

        $isWin = false;
        if ($game->getName() == $mysteryGame->getName()) {
            $isWin = true;
            $gameScore->setScore(100 - ($attempt) - $progression);
        } else {
            $gameScore->setAttempt($attempt + 1);
            $gameScore->setProgression($progression + $result['progression']);
        }

        $this->entityManager->persist($gameScore);
        $this->entityManager->flush();
        return $this->render('pages/find_the_game/index.html.twig', [
            'mysteryGame' => $mysteryGame,
            'gameScore' => $gameScore,
            'gameScoreFormated' => $gameScoreFormated,
            'newHints' => $result['newHints'],
            'searchHistory' => $result['searchHistory'],
            'isWin' => $isWin
        ]);
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

    private function formatSearchHistory($gameScore): array
    {
        $searchHistory = $gameScore->getSearchHistory();
        $searchHistory = $searchHistory ? json_decode($searchHistory, true) : [];
        // Parcourir les résultats et supprimer les '---' dans les éditeurs
        foreach ($searchHistory as &$game) {
            if (isset($game['hintMatch']['publishers'])) {
                // Séparer les éditeurs par ", ", retirer les '---' puis reconstruire la chaîne
                $publishers = explode(", ", $game['hintMatch']['publishers']);
                $publishers = array_filter($publishers, fn($p) => trim($p) !== '---'); // Supprimer les '---'
                $game['hintMatch']['publishers'] = implode(", ", $publishers);
            }
        }

        return $searchHistory;
    }

    private function compareGame(Game $game, $gameScore, $mysteryGame): array
    {
        $progression = 0;
        $name = $game->getName();
        $hintMatch = [];

        if (!$gameScore) {
            throw new \Exception("Aucun gameScore trouvé.");
        }

        $newHints = [];

        // 🔹 Gestion de l'âge
        if ($mysteryGame->getAge()) {
            $currentAge = $gameScore->getAge();
            $mysteryAge = $mysteryGame->getAge();
            $proposedAge = $game->getAge();

            if ($mysteryAge != $currentAge) {
                $newHint = $this->generateSimpleHint($currentAge, $mysteryAge, $proposedAge);
                if ($currentAge !== $newHint) {
                    $gameScore->setAge($newHint);
                    $newHints['age'] = $newHint;
                    if ($mysteryAge == $newHint) {
                        $hintMatch['age'] = 'Age: ' .  $newHint . " ans";
                        $progression += 1;
                    }
                }
            } else if ($mysteryAge == $proposedAge) {
                $hintMatch['age'] = 'Age: ' .  $proposedAge . " ans";
            }
        }

        // 🔹 Gestion du temps de jeu (playingTime)
        if ($mysteryGame->getPlayingTime()) {
            $currentPlayingTime = $gameScore->getPlayingTime();
            $mysteryPlayingTime = $mysteryGame->getPlayingTime();
            $proposedPlayingTime = $game->getPlayingTime();

            if ($mysteryPlayingTime != $currentPlayingTime) {
                $newHint = $this->generateSimpleHint($currentPlayingTime, $mysteryPlayingTime, $proposedPlayingTime);
                if ($currentPlayingTime !== $newHint) {
                    $gameScore->setPlayingTime($newHint);
                    $newHints['playingTime'] = $newHint;
                    if ($mysteryPlayingTime == $newHint) {
                        $hintMatch['playingTime'] = 'Durée: ' .  $newHint . " mn";
                        $progression += 1;
                    }
                }
            } else if ($mysteryPlayingTime == $proposedPlayingTime) {
                $hintMatch['playingTime'] = 'Durée: ' .  $proposedPlayingTime . " mn";
            }
        }

        // 🔹 Gestion de la date de sortie (yearPublished)
        if ($mysteryGame->getYearPublished()) {
            $currentYearPublished = $gameScore->getYearPublished();
            $mysteryYearPublished = $mysteryGame->getYearPublished();
            $proposedYearPublished = $game->getYearPublished();

            if ($mysteryYearPublished != $currentYearPublished) {
                $newHint = $this->generateSimpleHint($currentYearPublished, $mysteryYearPublished, $proposedYearPublished);
                if ($currentYearPublished !== $newHint) {
                    $gameScore->setYearPublished($newHint);
                    $newHints['yearPublished'] = $newHint;
                    if ($mysteryYearPublished == $newHint) {
                        $hintMatch['yearPublished'] = 'Sortie: ' .  $newHint;
                        $progression += 1;
                    }
                }
            } else if ($mysteryYearPublished == $proposedYearPublished) {
                $hintMatch['yearPublished'] = 'Sortie: ' .  $proposedYearPublished;
            }
        }

        // 🔹 Gestion du joueur minimum (minPlayers)
        if ($mysteryGame->getMinPlayers()) {
            $currentMinPlayers = $gameScore->getMinPlayers();
            $mysteryMinPlayers = $mysteryGame->getMinPlayers();
            $proposedMinPlayers = $game->getMinPlayers();

            if ($mysteryMinPlayers != $currentMinPlayers) {
                $newHint = $this->generateSimpleHint($currentMinPlayers, $mysteryMinPlayers, $proposedMinPlayers);
                if ($currentMinPlayers !== $newHint) {
                    $gameScore->setMinPlayers($newHint);
                    $newHints['minPlayers'] = $newHint;
                    if ($mysteryMinPlayers == $newHint) {
                        $hintMatch['minPlayers'] = 'Min: ' .  $newHint . 'j';
                        $progression += 1;
                    }
                }
            } else if ($mysteryMinPlayers == $proposedMinPlayers) {
                $hintMatch['minPlayers'] = 'Min: ' .  $proposedMinPlayers . 'j';
            }
        }

        // 🔹 Gestion du joueur maximum (maxPlayers)
        if ($mysteryGame->getMaxPlayers()) {
            $currentMaxPlayers = $gameScore->getMaxPlayers();
            $mysteryMaxPlayers = $mysteryGame->getMaxPlayers();
            $proposedMaxPlayers = $game->getMaxPlayers();

            if ($mysteryMaxPlayers != $currentMaxPlayers) {
                $newHint = $this->generateSimpleHint($currentMaxPlayers, $mysteryMaxPlayers, $proposedMaxPlayers);
                if ($currentMaxPlayers !== $newHint) {
                    $gameScore->setMaxPlayers($newHint);
                    $newHints['maxPlayers'] = $newHint;
                    if ($mysteryMaxPlayers == $newHint) {
                        $hintMatch['maxPlayers'] = 'Max: ' . $newHint . 'j';
                        $progression += 1;
                    }
                }
            } else if ($mysteryMaxPlayers == $proposedMaxPlayers) {
                $hintMatch['maxPlayers'] = 'Max: ' .  $proposedMaxPlayers . 'j';
            }
        }

        // 🔹 Gestion des Thèmes (categories)
        if ($mysteryGame->getCategoriesIndices()) {
            $currentCategories = json_decode($gameScore->getCategoriesIndices(), true);
            $mysteryCategories = json_decode($mysteryGame->getCategoriesIndices(), true);
            $proposedCategories = $game->getCategories();

            $categories = [];

            foreach ($proposedCategories as $proposedCategory) {
                $translatedName = $proposedCategory->getTranslatedName();

                foreach ($mysteryCategories as $index => $mysteryCategory) {
                    if ($mysteryCategory !== $currentCategories[$index]) {
                        if ($mysteryCategory === $translatedName) {
                            $currentCategories[$index] = $mysteryCategory;
                            $newHints['categories'] = $mysteryCategory;
                            $categories[] = $translatedName;
                            $progression += 2;
                        }
                    } else if ($mysteryCategory == $translatedName) {
                        $categories[] = $translatedName;
                    }
                }
            }
            if (!empty($categories)) {
                $hintMatch['categories'] = 'Thèmes: ' . implode(', ', array_unique($categories));
            }
            $gameScore->setCategoriesIndices(json_encode($currentCategories));
        }

        // 🔹 Gestion des catégories (subdomains)
        if ($mysteryGame->getSubdomainsIndices()) {
            $currentSubdomains = json_decode($gameScore->getSubdomainsIndices(), true) ?? [];
            $mysterySubdomains = json_decode($mysteryGame->getSubdomainsIndices(), true) ?? [];
            $proposedSubdomains = $game->getSubdomains();

            $subdomains = [];

            foreach ($proposedSubdomains as $proposedSubdomain) {
                $translatedName = $proposedSubdomain->getTranslatedName();

                foreach ($mysterySubdomains as $index => $mysterySubdomain) {
                    if ($mysterySubdomain !== $currentSubdomains[$index]) {
                        if ($mysterySubdomain === $translatedName) {
                            $currentSubdomains[$index] = $mysterySubdomain;
                            $newHints['subdomains'][] = $mysterySubdomain;
                            $subdomains[] = $translatedName;
                            $progression += 2;
                        }
                    } else if ($mysterySubdomain === $translatedName) {
                        $subdomains[] = $translatedName;
                    }
                }
            }
            if (!empty($subdomains)) {
                $hintMatch['subdomains'] = 'Catégories: ' . implode(', ', array_unique($subdomains));
            }
            $gameScore->setSubdomainsIndices(json_encode($currentSubdomains));
        }

        // 🔹 Gestion des mécanisme (mechanics)
        if ($mysteryGame->getMechanicsIndices()) {
            $currentMechanics = json_decode($gameScore->getMechanicsIndices(), true);
            $mysteryMechanics = json_decode($mysteryGame->getMechanicsIndices(), true);
            $proposedMechanics = $game->getMechanics();

            $mechanics = [];

            foreach ($proposedMechanics as $proposedMechanic) {
                $translatedName = $proposedMechanic->getTranslatedName();

                foreach ($mysteryMechanics as $index => $mysteryMechanic) {
                    if ($mysteryMechanic !== $currentMechanics[$index]) {
                        if ($mysteryMechanic === $translatedName) {
                            $currentMechanics[$index] = $mysteryMechanic;
                            $newHints['mechanics'] = $mysteryMechanic;
                            $mechanics[] =  $translatedName;
                            $progression += 2;
                        }
                    } else if ($mysteryMechanic == $translatedName) {
                        $mechanics[] =  $translatedName;
                    }
                }
            }
            if (!empty($mechanics)) {
                $hintMatch['mechanics'] = 'Mécanismes: ' . implode(', ', array_unique($mechanics));
            }
            $gameScore->setMechanicsIndices(json_encode($currentMechanics));
        }

        // 🔹 Gestion des créateurs (designers)
        if ($mysteryGame->getDesignersIndices()) {
            $currentDesigners = json_decode($gameScore->getDesignersIndices(), true);
            $mysteryDesigners = json_decode($mysteryGame->getDesignersIndices(), true);
            $proposedDesigners = $game->getDesigners();

            $result = $this->generateComplexHint($currentDesigners, $mysteryDesigners, $proposedDesigners);
            $gameScore->setDesignersIndices(json_encode($result['currentHints']));
            if ($result['find']) {
                $newHints['designers'] = $result['currentHints'];
                $hintMatch['designers'] = 'Créateurs: ' . implode(', ', $result['currentHints']);
                $progression += count(array_filter($result['currentHints'], fn($hint) => $hint !== '---')) * 2;
            }
            // else if ($result['match']) {
            //     $hintMatch['designers'] = 'Créateurs: ' . implode(', ', $result['currentHints']);
            // }
        }

        // 🔹 Gestion des Illustrateurs (artists)
        if ($mysteryGame->getArtistsIndices()) {
            $currentArtists = json_decode($gameScore->getArtistsIndices(), true);
            $mysteryArtists = json_decode($mysteryGame->getArtistsIndices(), true);
            $proposedArtists = $game->getArtists();

            $result = $this->generateComplexHint($currentArtists, $mysteryArtists, $proposedArtists);
            $gameScore->setArtistsIndices(json_encode($result['currentHints']));
            if ($result['find']) {
                $newHints['artists'] = $result['currentHints'];
                $hintMatch['artists'] = 'Illustrateurs: ' . implode(', ', $result['currentHints']);
                $progression += count(array_filter($result['currentHints'], fn($hint) => $hint !== '---')) * 2;
            }
            //  else if ($result['match']) {
            //     $hintMatch['artists'] = 'Illustrateurs: ' . implode(', ', $result['currentHints']);
            // }
        }

        // 🔹 Gestion des Développeurs (developers)
        if ($mysteryGame->getDevelopersIndices()) {
            $currentDevelopers = json_decode($gameScore->getDevelopersIndices(), true);
            $mysteryDevelopers = json_decode($mysteryGame->getDevelopersIndices(), true);
            $proposedDevelopers = $game->getDevelopers();

            $result = $this->generateComplexHint($currentDevelopers, $mysteryDevelopers, $proposedDevelopers);
            $gameScore->setDevelopersIndices(json_encode($result['currentHints']));
            if ($result['find']) {
                $newHints['developers'] = $result['currentHints'];
                $hintMatch['developers'] = 'Développeurs: ' . implode(', ', $result['currentHints']);
                $progression += count(array_filter($result['currentHints'], fn($hint) => $hint !== '---')) * 2;
            }
            // else if ($result['match']) {
            //     $hintMatch['developers'] = 'Développeurs: ' . implode(', ', $result['currentHints']);
            // }
        }

        // 🔹 Gestion des Designers (graphicDesigners)
        if ($mysteryGame->getGraphicDesignersIndices()) {
            $currentGraphicDesigners = json_decode($gameScore->getGraphicDesignersIndices(), true);
            $mysteryGraphicDesigners = json_decode($mysteryGame->getGraphicDesignersIndices(), true);
            $proposedGraphicDesigners = $game->getGraphicDesigners();

            $result = $this->generateComplexHint($currentGraphicDesigners, $mysteryGraphicDesigners, $proposedGraphicDesigners);
            $gameScore->setGraphicDesignersIndices(json_encode($result['currentHints']));
            if ($result['find']) {
                $newHints['graphicDesigners'] = $result['currentHints'];
                $hintMatch['graphicDesigners'] = 'Designers: ' . implode(', ', $result['currentHints']);
                $progression += count(array_filter($result['currentHints'], fn($hint) => $hint !== '---')) * 2;
            }
            // else if ($result['match']) {
            //     $hintMatch['graphicDesigners'] = 'Designers: ' . implode(', ', $result['currentHints']);
            // }
        }

        // 🔹 Gestion des Editeurs (publishers)
        if ($mysteryGame->getPublishersIndices()) {
            $currentPublishers = json_decode($gameScore->getPublishersIndices(), true);
            $mysteryPublishers = json_decode($mysteryGame->getPublishersIndices(), true);
            $proposedPublishers = $game->getPublishers();

            $result = $this->generateComplexHint($currentPublishers, $mysteryPublishers, $proposedPublishers);
            $gameScore->setPublishersIndices(json_encode($result['currentHints']));
            if ($result['find']) {
                $newHints['publishers'] = $result['currentHints'];
                $hintMatch['publishers'] = 'Editeurs: ' . implode(', ', $result['currentHints']);
                $progression += count(array_filter($result['currentHints'], fn($hint) => $hint !== '---')) * 2;
            } else if ($result['match']) {
                $hintMatch['publishers'] = 'Editeurs: ' . implode(', ', $result['currentHints']);
            }
        }

        // 🔹 Gestion des Récompenses (honors)
        if ($mysteryGame->getHonorsIndices()) {
            $currentHonors = json_decode($gameScore->getHonorsIndices(), true);
            $mysteryHonors = json_decode($mysteryGame->getHonorsIndices(), true);
            $proposedHonors = $game->getHonorGames();

            foreach ($proposedHonors as $proposedHonor) {
                foreach ($mysteryHonors as $index => $mysteryHonor) {
                    if ($mysteryHonor !== $currentHonors[$index]) {
                        if ($mysteryHonor === $proposedHonor->getHonor()->getName()) {
                            $currentHonors[$index] = $mysteryHonor;
                            $hintMatch['honors'] = 'Récompenses: ' . $newHints['honors'] = $mysteryHonor;
                            $progression += 2;
                        }
                    } else if ($mysteryHonor == $proposedHonor) {
                        $hintMatch['honors'] = 'Récompenses: ' . $newHints['honors'] = $mysteryHonor;
                    }
                }
            }
            $gameScore->setHonorsIndices(json_encode($currentHonors));
        }

        $searchHistory = $gameScore->getSearchHistory();

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
            $gameScore->setSearchHistory(json_encode($searchHistory, JSON_UNESCAPED_UNICODE));
        }

        $searchHistory = $this->formatSearchHistory($gameScore);

        return compact('newHints', 'searchHistory', 'progression');
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
        $match = false;
        foreach ($proposeHints as $proposeHint) {
            foreach ($mysteryHints as $index => $mysteryHint) {
                if ($mysteryHint !== $currentHints[$index]) {
                    if ($mysteryHint === $proposeHint->getName()) {
                        $currentHints[$index] = $mysteryHint;
                        $find = true;
                    }
                } else if ($mysteryHint == $proposeHint) {
                    $match = true;
                }
            }
        }

        return [
            'currentHints' => $currentHints,
            'find' => $find,
            'match' => $match
        ];
    }
}
