<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
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
        ->setPermission(Action::EDIT, 'ROLE_ADMIN')
        ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN')
        ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
    ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Jeu')
            ->setEntityLabelInPlural('Jeux')
            ->setDateFormat('...')
            // ...
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            NumberField::new('gameId')->setLabel('Id')->onlyOnIndex(),
            NumberField::new('yearPublished')->setLabel('Année de publication'),
            TextField::new('name')->setLabel('Nom'),
            TextField::new('allNames')->setLabel('Autres noms')->setDisabled(),
            NumberField::new('minPlayers')->setLabel('Joueur min.'),
            NumberField::new('maxPlayers')->setLabel('Joueur max.'),
            NumberField::new('playingTime')->setLabel('Temps de jeu'),
            NumberField::new('age')->setLabel('Age'),
            TextareaField::new('description')->setLabel('Description')->onlyOnForms(),
            AssociationField::new('publishers')->setLabel('Editeurs')->setDisabled(),
            AssociationField::new('artists')->setLabel('Créateurs')->setDisabled(),
            AssociationField::new('developers')->setLabel('Développeurs')->setDisabled(),
            AssociationField::new('designers')->setLabel('Designers')->setDisabled(),
            AssociationField::new('graphicDesigners')->setLabel('Designers graphique')->setDisabled(),
            AssociationField::new('categories')->setLabel('Catégories')->setDisabled(),
            AssociationField::new('families')->setLabel('Familles')->setDisabled(),
            AssociationField::new('mechanics')->setLabel('Style')->setDisabled(),
            AssociationField::new('subdomains')->setLabel('Domaine')->setDisabled()
        ];
    }
}
