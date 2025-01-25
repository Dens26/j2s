<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Entity\Family;
use App\Entity\Game;
use App\Entity\Mechanic;
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
        yield MenuItem::linkToRoute('Retourner sur le site', 'fas fa-arrow-left', 'app_home'); // Flèche pour revenir
        yield MenuItem::linkToCrud('Utilisateurs', 'fas fa-users', User::class); // Groupe d'utilisateurs
        yield MenuItem::linkToCrud('Jeux', 'fas fa-dice', Game::class); // Icône représentant un dé pour les jeux
        yield MenuItem::linkToCrud('Catégories', 'fas fa-list', Category::class); // Liste pour les catégories
        yield MenuItem::linkToCrud('Familles', 'fas fa-sitemap', Family::class); // Schéma pour les familles
        yield MenuItem::linkToCrud('Styles de jeu', 'fas fa-gamepad', Mechanic::class); // Manette pour les styles de jeu
        yield MenuItem::linkToCrud('Domaines', 'fas fa-globe', Subdomain::class); // Globe pour les domaines

    }
}
