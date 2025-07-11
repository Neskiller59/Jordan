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
        yield TextField::new('name', 'Nom');
        yield ImageField::new('image', 'Image')
            ->setBasePath('uploads/models') // adapter selon ton dossier d’upload
            ->setUploadDir('public/uploads/models')
            ->setUploadedFileNamePattern('[randomhash].[extension]')
            ->setRequired(false);
        yield MoneyField::new('Prix', 'Prix')->setCurrency('EUR');
    }
}
