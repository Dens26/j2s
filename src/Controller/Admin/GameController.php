<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class GameController extends AbstractController
{
    #[Route('/admin-game-create', name: 'admin_game_create')]
    public function index(): Response
    {
        return $this->render('admin/game/create.html.twig');
    }
}
