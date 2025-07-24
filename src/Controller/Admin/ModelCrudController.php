<?php

namespace App\Controller\Admin;

use App\Entity\Model;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;

class ModelCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Model::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', 'Nom'),
            ImageField::new('image', 'Image')
                ->setBasePath('images/product')
                ->setUploadDir('public/images/product')
                ->setUploadedFileNamePattern('[randomhash].[extension]') 
                ->setRequired(false),
            MoneyField::new('prix', 'Prix')->setCurrency('EUR')
            ->setStoredAsCents(false),

        ];
    }
}
