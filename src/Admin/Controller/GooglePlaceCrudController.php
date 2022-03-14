<?php

namespace App\Admin\Controller;

use App\Entity\GooglePlace;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class GooglePlaceCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GooglePlace::class;
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
