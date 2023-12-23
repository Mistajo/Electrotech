<?php

namespace App\Form;

use App\Entity\Image;
use App\Entity\Product;
use App\Entity\Category;
use Doctrine\DBAL\Types\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference')
            ->add('category', EntityType::class, [
                // looks for choices from this entity
                'class' => Category::class,

                'placeholder' => 'Choisissez la catégorie',

                // uses the User.username property as the visible option string
                'choice_label' => 'name',
            ])
            ->add('name', TypeTextType::class,)
            ->add('quantity')
            ->add('image', EntityType::class, [
                'placeholder' => "Choisissez l'image'",
                // looks for choices from this entity
                'class' => Image::class,
                // uses the User.username property as the visible option string
                'choice_label' => 'Image',

                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('shortDescription', TextareaType::class)
            ->add('longDescription', TextareaType::class)
            ->add('brand', TextType::class)
            ->add('sellingPrice')
            ->add('discountPrice');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
