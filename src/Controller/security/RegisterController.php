<?php

namespace App\Controller\security;

use App\Entity\User;
use App\Form\RegisterUserType;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        if ($this->getUser()){
            return $this->redirectToRoute('app_home');
        }
        $user = new User();
        $form = $this->createForm(RegisterUserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setCreatedAt(new DateTimeImmutable());
            $user->setUpdatedAt($user->getCreatedAt());
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur enregistré avec succès !');
            return $this->redirectToRoute('app_register');
        }

        return $this->render('security/register/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
