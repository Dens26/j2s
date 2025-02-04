<?php

namespace App\Controller\Admin;

use App\Entity\Artist;
use App\Entity\Category;
use App\Entity\Designer;
use App\Entity\Developer;
use App\Entity\Family;
use App\Entity\Game;
use App\Entity\GraphicDesigner;
use App\Entity\Honor;
use App\Entity\Mechanic;
use App\Entity\Publisher;
use App\Entity\Subdomain;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractDashboardController
{
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {
        // return parent::index();

        // Option 1. You can make your dashboard redirect to some common page of your backend
        //
        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(UserCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('J2S--ADMINISTRATION');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute('Retourner sur le site', 'fas fa-arrow-left', 'app_home');
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class);
        yield MenuItem::linkToCrud('Jeux', 'fas fa-chess', Game::class);  // "fa-dice" -> "fa-chess" pour un style de jeu plus représentatif
        yield MenuItem::linkToCrud('Catégories', 'fas fa-tags', Category::class);  // "fa-list" -> "fa-tags" pour des catégories
        yield MenuItem::linkToCrud('Familles', 'fas fa-sitemap', Family::class);
        yield MenuItem::linkToCrud('Styles de jeu', 'fas fa-gamepad', Mechanic::class);
        yield MenuItem::linkToCrud('Domaines', 'fas fa-cogs', Subdomain::class);  // "fa-globe" -> "fa-cogs" pour un aspect plus technique
        yield MenuItem::linkToCrud('Créateurs', 'fas fa-pencil-alt', Designer::class);  // "fa-globe" -> "fa-pencil-alt" pour un créateur
        yield MenuItem::linkToCrud('Illustrateurs', 'fas fa-paint-brush', Artist::class);  // "fa-globe" -> "fa-paint-brush" pour un illustrateur
        yield MenuItem::linkToCrud('Développeurs', 'fas fa-code', Developer::class);  // "fa-globe" -> "fa-code" pour un développeur
        yield MenuItem::linkToCrud('Designers', 'fas fa-pen-square', GraphicDesigner::class);  // "fa-globe" -> "fa-pen-square" pour un designer graphique
        yield MenuItem::linkToCrud('Récompenses', 'fas fa-trophy', Honor::class);  // "fa-globe" -> "fa-trophy" pour une récompense
        yield MenuItem::linkToCrud('Editeurs', 'fas fa-book', Publisher::class);  // "fa-globe" -> "fa-book" pour un éditeur
    }
}
