<?php

namespace App\Controller\pages;

use App\Entity\GameScore;
use App\Entity\MysteryGame;
use App\Entity\Status;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FindTheGameController extends AbstractController
{
    #[Route('/findthegame', name: 'app_find_the_game')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        /** @var User $user */
        $suser = $this->getUser();

        $autoStatus = $entityManager->getRepository(Status::class)->findOneBy(['name' => 'auto']);
        $mysteryGame = $entityManager->getRepository(MysteryGame::class)->findOneBy(['status' => $autoStatus]);

        /** @var GameScore $gameScore */
        $gameScore = new GameScore();
        $gameScore
            ->setUser($user->getId())
            ->setGame($mysteryGame->getId())
            ->setYearPublished('----')
            ->setMinPlayers('--')
            ->setMaxPlayers('--')
            ->setPlayingTime('--')
            ->setAge('--')
            ->setCategoriesIndices('[---]')
            ->setSubdomainsIndices('[---]')
            ->setMechanicsIndices('[---]')
            ->setDesignersIndices('[---]')
            ->setArtistsIndices('[---]')
            ->setGraphicDesignersIndices('[---]')
            ->setHonorsIndices('[---]')
            ->setPublishersIndices('[---]')
            ->setDevelopersIndices('[---]')
            ->setSearchHistory('')
        ;
        return $this->render('pages/find_the_game/index.html.twig', [
            'mysteryGame' => $mysteryGame
        ]);
    }
}
