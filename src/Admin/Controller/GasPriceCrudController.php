<?php

namespace App\Admin\Controller;

use App\Entity\GasPrice;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GasPriceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GasPrice::class;
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
