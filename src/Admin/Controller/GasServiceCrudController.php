<?php

namespace App\Admin\Controller;

use App\Entity\GasService;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GasServiceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GasService::class;
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