<?php

namespace App\Controller\pages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FindTheGameController extends AbstractController
{
    #[Route('/findthegame', name: 'app_find_the_game')]
    public function index(): Response
    {
        return $this->render('pages/find_the_game/index.html.twig');
    }
}
