<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\PasswordResetRequestType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PasswordResetController extends AbstractController
{
    #[Route('/password/reset', name: 'app_password_reset')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PasswordResetRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $email = $form->get('email')->getData();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user && $user instanceof USer){
                // Génération du Token de connexion et de la date d'expiration
                $user->setPasswordResetToken(bin2hex(random_bytes(32)));
                $user->setPasswordResetTokenExpiration(new DateTimeImmutable('+1 hour'));
                $entityManager->flush();

                // Génération du lien de reinitialisation du mot de passe
                $resetLink = $this->generateUrl('password_reset_confirm', [
                    'token' => $user->getPasswordResetToken()
                ], UrlGeneratorInterface::ABSOLUTE_URL);

                // Envoyer l'email avec un lien de connexion
                // ....
                // ....
                // ....
            }
        }
        return $this->render('password_reset/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
