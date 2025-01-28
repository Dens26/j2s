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
        $minPlayers = substr($data['players'], 0, 1);
        $maxPlayers = substr($data['players'], 0, 1);
        $category = '[';
            foreach($data['categories'] as $cat){
                $category .= '"' . $cat . '",';
            }
            $category .= ']';

        $mysteryGame = new MysteryGame();

        $mysteryGame
            ->setCreatedAt(new DateTimeImmutable())
            ->setUpdatedAt($mysteryGame->getCreatedAt())
            ->setName($data['name'])
            ->setYearPublished($data['yearPublished'])
            ->setMinPlayers($minPlayers)
            ->setMaxPlayers($maxPlayers)
            ->setPlayingTime($data['playingTime'])
            ->setAge($data['age'])
            ->setCategoriesIndices($category)
            // ->setSubdomainsIndices($data['subdomains'])
            // ->setMechanicsIndices($data['mechanics'])
            // ->setDesignersIndices($data['designers'])
            // ->setArtistsIndices($data['artists'])
            // ->setGraphicDesignersIndices($data['graphicDesigners'])
            // ->setHonorsIndices($data['honors'])
            // ->setPublishersIndices($data['publishers'])
            ;
        dd($mysteryGame);
        return $this->render('admin/mystery_game/show.html.twig');
    }
}
