<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class GameCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Game::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->setPermission(Action::EDIT, 'ROLE_ADMIN')   // Les admins peuvent éditer
        ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN')             // Interdire la création
        ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')          // Interdire la suppression
    ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            NumberField::new('gameId')->setLabel('Id')->onlyOnIndex(),
            NumberField::new('yearPublished')->setLabel('Année de publication'),
            TextField::new('name')->setLabel('Nom'),
            TextField::new('allNames')->setLabel('Autres noms'),
            NumberField::new('minPlayers')->setLabel('Joueur min.'),
            NumberField::new('maxPlayers')->setLabel('Joueur max.'),
            NumberField::new('playingTime')->setLabel('Temps de jeu'),
            NumberField::new('age')->setLabel('Age'),
            TextareaField::new('description')->setLabel('Description'),
            
            /*
            allNames
            year_published
            min_players
            max_players
            playing_time
            age
            description
            thumbnail
            image
            */
        ];
    }
}
