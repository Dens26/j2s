<?php

namespace App\Controller\pages;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class HelpController extends AbstractController
{
    #[Route('/help', name: 'app_help')]
    public function index(): Response
    {
        return $this->render('pages/help/index.html.twig', [
            'controller_name' => 'HelpController',
        ]);
    }
}
