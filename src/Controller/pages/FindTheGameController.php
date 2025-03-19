<?php

namespace App\Controller\pages;

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
        $autoStatus = $entityManager->getRepository(Status::class)->findOneBy(['name' => 'auto']);
        $mysteryGame = $entityManager->getRepository(MysteryGame::class)->findOneBy(['status' => $autoStatus]);
        
        return $this->render('pages/find_the_game/index.html.twig', [
            'mysteryGame' => $mysteryGame
        ]);
    }
}
