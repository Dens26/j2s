<?php

namespace App\Controller\security;

use App\Classe\MailClass;
use App\Entity\User;
use App\Form\PasswordResetRequestType;
use App\Form\ResetPasswordType;
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
    public function request(Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PasswordResetRequestType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $email = $form->get('email')->getData();
            $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($user && $user instanceof USer) {
                // Génération du Token de connexion et de la date d'expiration
                $user->setPasswordResetToken(bin2hex(random_bytes(32)));
                $user->setPasswordResetTokenExpiration(new DateTimeImmutable('+1 hour'));
                $entityManager->flush();

                // Génération du lien de reinitialisation du mot de passe
                $resetLink = $this->generateUrl('password_reset_confirm', [
                    'token' => $user->getPasswordResetToken()
                ], UrlGeneratorInterface::ABSOLUTE_URL);

                // Envoie de l'email avec un lien de connexion
                $mail = new MailClass();
                $mail->resetPassword($email, $user->getFirstname() . ' ' . $user->getLastname(), 'Réinitialisation du mot de passe', "<a href='$resetLink'>Réinitialiser</a>");

                $this->addFlash('success', 'Un email de réinitialisation a été envoyé.');
                return $this->redirectToRoute('app_login');
            }
            $this->addFlash('danger', 'L\'adresse e-mail que vous avez renseignée ne correspond à aucun compte existant');
        }
        return $this->render('security/password_reset/request.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/password-reset/confirm/{token}', name: 'app_password_reset_confirm')]
    public function confirm(string $token, Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->findOneBy(['passwordResetToken' => $token]);
        if ($user instanceof User) {

            // Délai d'expiration du token expiré
            if (!$user || $user->getPasswordResetTokenExpiration() < new DateTimeImmutable()) {
                $this->addFlash('danger', 'Le délai de la demande de réinitialisation a expiré');
                return $this->redirectToRoute('app_password_reset');
            }

            $form = $this->createForm(ResetPasswordType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $user->setUpdatedAt(new DateTimeImmutable());
                $user->setPasswordResetToken(null);
                $user->setPasswordResetTokenExpiration(null);
                $entityManager->flush();

                $this->addFlash('success', 'Votre mot de passe a été mis à jour');
                return $this->redirectToRoute('app_login');
            }

            return $this->render('security/password_reset/confirm.html.twig', [
                'form' => $form->createView()
            ]);
        }
    }
}
