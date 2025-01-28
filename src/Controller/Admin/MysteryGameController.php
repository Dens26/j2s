<?php

namespace App\Controller\Admin;

use App\Entity\MysteryGame;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MysteryGameController extends AbstractController
{
    #[Route('/admin-mystery-game-show', name: 'admin_mystery_game_show')]
    public function index(Request $request): Response
    {
        $data = $request->request->all();

        $mysteryGame = $this->setMysteryGame($data);

        dd($mysteryGame);
        return $this->render('admin/mystery_game/show.html.twig');
    }

    private function setMysteryGame(array $data): MysteryGame
    {
        $players = explode('-', $data['players']);
        $minPlayers = (int)($players[0] ?? 1);
        $maxPlayers = (int)($players[1] ?? $minPlayers);

        $mysteryGame = new MysteryGame();
        $mysteryGame
            ->setCreatedAt(new DateTimeImmutable())
            ->setUpdatedAt($mysteryGame->getCreatedAt())
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
                $mysteryGame->$method($this->encodeJsonOrNull($data[$key]));
            }
        }
    }

    private function encodeJsonOrNull(?array $data): ?string
    {
        return empty($data) ? null : json_encode($data);
    }
}