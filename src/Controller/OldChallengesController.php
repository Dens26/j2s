<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OldChallengesController extends AbstractController
{
    #[Route('/oldchallenges', name: 'app_old_challenges')]
    public function index(): Response
    {
        return $this->render('old_challenges/index.html.twig');
    }
}
