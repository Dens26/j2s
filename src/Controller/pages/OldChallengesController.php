<?php

namespace App\Controller\pages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OldChallengesController extends AbstractController
{
    #[Route('/oldchallenges', name: 'app_old_challenges')]
    public function index(): Response
    {
        return $this->render('pages/old_challenges/index.html.twig');
    }
}
