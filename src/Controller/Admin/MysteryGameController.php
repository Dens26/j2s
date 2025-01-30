<?php

namespace App\Controller\Admin;

use App\Entity\MysteryGame;
use App\Entity\Status;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MysteryGameController extends AbstractController
{
    #[Route('/admin-mystery-game-index', name: 'admin_mystery_game_index')]
    public function index()
    {
        return $this->render('admin/game/index.html.twig');
    }

    #[Route('/admin-mystery-game-show', name: 'admin_mystery_game_show')]
    public function show(Request $request, EntityManagerInterface $entityManager): Response
    {
        $streamStatus = $entityManager->getRepository(Status::class)->findOneBy(['name' => 'stream']);
        $data = $request->request->all();

        $mysteryGame = $this->setMysteryGame($data, $streamStatus);

        $entityManager->persist($mysteryGame);
        $entityManager->flush();

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
    public function pushToAuto(EntityManagerInterface $entityManager): Response
    {
        $streamStatus = $entityManager->getRepository(Status::class)->findOneBy(['name' => 'stream']);
        $autoStatus = $entityManager->getRepository(Status::class)->findOneBy(['name' => 'auto']);
        $archivedStatus = $entityManager->getRepository(Status::class)->findOneBy(['name' => 'archived']);

        $mysteryGameAuto = $entityManager->getRepository(MysteryGame::class)->findOneBy(['status' => $autoStatus]);
        if ($mysteryGameAuto) {
            $mysteryGameAuto->setStatus($archivedStatus);
            $entityManager->persist($mysteryGameAuto);
        }
        $mysteryGame = $entityManager->getRepository(MysteryGame::class)->findOneBy(['status' => $streamStatus]);
        if ($mysteryGame) {
            $mysteryGame->setStatus($autoStatus);
            $entityManager->persist($mysteryGame);
        }
        $entityManager->flush();
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
}
