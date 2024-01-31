<?php

namespace App\Form;


use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType as TypeTextType;

class ProductFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('reference', NumberType::class)
            ->add('category', EntityType::class, [
                // looks for choices from this entity
                'class' => Category::class,
                'placeholder' => 'Choisissez la catégorie',
                // uses the User.username property as the visible option string
                'choice_label' => 'name',
            ])
            ->add('name', TypeTextType::class,)
            ->add('quantity', NumberType::class)
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'allow_delete' => true,
                'download_label' => false,
                'download_uri' => false,
                'image_uri' => false,
                'imagine_pattern' => false,
                'asset_helper' => true,
                // used to render a select box, check boxes or radios
                // 'multiple' => true,
                // 'expanded' => true,
            ])
            ->add('shortDescription', TextareaType::class)
            ->add('longDescription', TextareaType::class)
            ->add('brand', TextType::class)
            ->add('sellingPrice', MoneyType::class)
            ->add('discountPrice', MoneyType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
