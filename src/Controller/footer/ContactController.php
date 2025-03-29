<?php

namespace App\Controller\footer;

use App\DTO\ContactDTO;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {
        $contactDTO = new ContactDTO();
        $form = $this->createForm(ContactType::class, $contactDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->addFlash('success', 'Message envoyé avec succès !');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('pages/contact/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
