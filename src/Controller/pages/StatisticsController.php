<?php

namespace App\Controller\pages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class StatisticsController extends AbstractController
{
    #[Route('/statistics', name: 'app_statistics')]
    public function index(): Response
    {
        return $this->render('pages/statistics/index.html.twig', [
            'controller_name' => 'StatisticsController',
        ]);
    }
}
