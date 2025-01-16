<?php

namespace App\Controller\security;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // Redirection vers la page d'accueil si l'utilisateur est déjà connecté
        if ($this->getUser()){
            return $this->redirectToRoute('app_home');
        }

        // Gestion des erreurs
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupération du dernier utilisateur
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login/index.html.twig', [
            'error' => $error,
            'last_username' => $lastUsername
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): never {

    }
}
