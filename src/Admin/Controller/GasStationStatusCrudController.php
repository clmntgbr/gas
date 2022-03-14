<?php

namespace App\Admin\Controller;

use App\Entity\GasStationStatus;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GasStationStatusCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GasStationStatus::class;
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
