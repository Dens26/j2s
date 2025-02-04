<?php

namespace App\Controller\footer;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class LegalController extends AbstractController
{
    #[Route('/legal-notices', name: 'app_legal_notices')]
    public function legalNotices(): Response
    {
        return $this->render('pages/legal/legal_notices.html.twig');
    }
    
    #[Route('/confidentiality', name: 'app_confidentiality')]
    public function confidentiality(): Response
    {
        return $this->render('pages/legal/confidentiality.html.twig');
    }

    #[Route('/cgu', name: 'app_cgu')]
    public function generalConditionsOfUse(): Response
    {
        return $this->render('pages/legal/cgu.html.twig');
    }

    #[Route('/contact', name: 'app_contact')]
    public function contact(): Response
    {
        return $this->render('pages/legal/contact.html.twig');
    }
}
