<?php

namespace App\Controller\Admin;

use App\Entity\Game;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
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
            BooleanField::new('checked')->setLabel('Vérifié')->onlyOnIndex()->setDisabled(),
            BooleanField::new('checked')->setLabel('Vérifié')->onlyOnForms(),
            NumberField::new('gameId')->setLabel('Id')->onlyOnIndex(),
            NumberField::new('yearPublished')->setLabel('Année de publication'),
            TextField::new('name')->setLabel('Nom'),
            TextField::new('allNames')->setLabel('Liste des noms')->setDisabled()->onlyOnForms(),
            NumberField::new('minPlayers')->setLabel('Joueur min.')->onlyOnForms(),
            NumberField::new('maxPlayers')->setLabel('Joueur max.')->onlyOnForms(),
            NumberField::new('playingTime')->setLabel('Temps de jeu')->onlyOnForms(),
            NumberField::new('age')->setLabel('Age')->onlyOnForms(),
            TextareaField::new('description')->setLabel('Description')->onlyOnForms(),
            AssociationField::new('designers')->setLabel('Créateurs')->setFormTypeOption('by_reference', false)->onlyOnForms(),
            AssociationField::new('artists')->setLabel('Artistes')->setFormTypeOption('by_reference', false)->onlyOnForms(),
            AssociationField::new('developers')->setLabel('Développeurs')->setFormTypeOption('by_reference', false)->onlyOnForms(),
            AssociationField::new('graphicDesigners')->setLabel('Designers graphique')->setFormTypeOption('by_reference', false)->onlyOnForms(),
            AssociationField::new('categories')->setLabel('Catégories')->setFormTypeOption('by_reference', false)->onlyOnForms(),
            AssociationField::new('families')->setLabel('Familles')->setFormTypeOption('by_reference', false)->onlyOnForms(),
            AssociationField::new('mechanics')->setLabel('Style')->setFormTypeOption('by_reference', false)->onlyOnForms(),
            AssociationField::new('subdomains')->setLabel('Domaine')->setFormTypeOption('by_reference', false)->onlyOnForms(),
            AssociationField::new('publishers')->setLabel('Editeurs')->setFormTypeOption('by_reference', false)->onlyOnForms()
        ];
    }
}
