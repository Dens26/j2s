<?php

namespace App\Controller\Admin\Crud;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions 
        ->setPermission(Action::INDEX, 'ROLE_MANAGER')
        ->setPermission(Action::EDIT, 'ROLE_MANAGER')
        ->setPermission(Action::NEW, 'ROLE_SUPER_ADMIN')
        ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
    ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
            ->setDateFormat('...')
            // ...
        ;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            ChoiceField::new('roles')
            ->setLabel('Rôles')
            ->setChoices([
                'Utilisateur' => 'ROLE_USER',
                'Admin' => 'ROLE_ADMIN',
                'Manager' => 'ROLE_MANAGER',
            ])
            ->allowMultipleChoices(),
            TextField::new('firstname')->setLabel('Prénom'),
            TextField::new('lastname')->setLabel('Nom'),
            TextField::new('username')->setLabel('Nom d\'utilisateur'),
            EmailField::new('email')->setLabel('E-mail')->onlyOnIndex(),
            DateTimeField::new('createdAt')->setLabel('Créé le')
        ];
    }

}
