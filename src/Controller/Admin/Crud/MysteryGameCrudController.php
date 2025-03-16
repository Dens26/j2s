<?php

namespace App\Controller\Admin\Crud;

use App\Entity\MysteryGame;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class MysteryGameCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return MysteryGame::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->setPermission(Action::EDIT, 'ROLE_SUPER_ADMIN')
        ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN')
        ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
    ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Jeu mystère')
            ->setEntityLabelInPlural('Jeux mystères<br/>(Contacter super_admin via la partie contact du site pour modification)')
            ->setDateFormat('...')
            // ...
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('status')->setLabel('Statut'),
            TextField::new('name')->setLabel('Nom')
        ];
    }
}
