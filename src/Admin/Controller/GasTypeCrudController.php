<?php

namespace App\Admin\Controller;

use App\Entity\GasType;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GasTypeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GasType::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
