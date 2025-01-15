<?php

namespace App\Controller\pages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RankingController extends AbstractController
{
    #[Route('/ranking', name: 'app_ranking')]
    public function index(): Response
    {
        return $this->render('pages/ranking/index.html.twig', [
            'controller_name' => 'RankingController',
        ]);
    }
}
