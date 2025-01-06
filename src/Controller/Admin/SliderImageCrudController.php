<?php

namespace App\Controller\Admin;

use App\Entity\SliderImage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Vich\UploaderBundle\Form\Type\VichImageType;

#[IsGranted('ROLE_ADMIN')]
class SliderImageCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return SliderImage::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('imageName', 'Nom de l\'image')
                ->hideOnForm(),
            TextField::new('imageFile', 'Image')
                ->setFormType(VichImageType::class)
                ->onlyOnForms(),
            ImageField::new('imageName', 'Image')
                ->setBasePath('/images/slider_image')
                ->onlyOnIndex(),
        ];
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Image')
            ->setEntityLabelInPlural('Images')
            ->setPageTitle(Crud::PAGE_INDEX, 'Liste des %entity_label_plural%')
            ->setPageTitle(Crud::PAGE_EDIT, 'Modifier une %entity_label_singular%')
            ->setPageTitle(Crud::PAGE_NEW, 'Ajouter une %entity_label_singular%');
    }
}
