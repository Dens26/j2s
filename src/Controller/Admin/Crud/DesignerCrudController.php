<?php

namespace App\Controller\Admin\Crud;

use App\Entity\Designer;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DesignerCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Designer::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        return $actions
        ->setPermission(Action::EDIT, 'ROLE_ADMIN')
        ->setPermission(Action::NEW, 'ROLE_ADMIN')
        ->setPermission(Action::DELETE, 'ROLE_SUPER_ADMIN')
    ;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Créateur')
            ->setEntityLabelInPlural('Créateurs')
            ->setDateFormat('...')
            // ...
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name')->setLabel('Nom'),
        ];
    }
}
