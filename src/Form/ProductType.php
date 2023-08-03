<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use App\Form\Type\PriceType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use App\Form\DataTransformer\CentimesTransformer;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
                'attr' => ['placeholder' => 'Taper le nom du produit']
            ])
            ->add('shortDescription', TextareaType::class, [
                'label' => 'Description courte',
                'attr' => ['placeholder' => 'Taper une description assez courte mais parlante pour le visiteur']
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix du produit',
                'attr' => [
                    'placeholder' => 'Taper le prix du produit en €'
                ],
                'divisor' => 100
            ])
            // ->add('price', PriceType::class, [
            //     'label' => 'Prix du produit',
            //     'attr' => [
            //         'placeholder' => 'Taper le prix du produit en €'
            //     ]
            // ])
            ->add('mainPicture', UrlType::class, [
                'label' => 'Image du produit',
                'attr' => ['placeholder' => 'Tapez une URL d\'image !']
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie',
                'placeholder' => '-- Choisir une catégorie --',
                'class' => Category::class,
                'choice_label' => function(Category $category){
                    return strtoupper($category->getName());
                }
            ]);
        
        //$builder->get('price')->addModelTransformer(new CentimesTransformer);
        //$builder->get('price')->addViewTransformer();

        // $builder->addEventListener(FormEvents::POST_SUBMIT, function(FormEvent $event){
        //     //$form = $event->getForm();

        //     /** @var Product */
        //     $product = $event->getData();

        //     if($product->getPrice() !== null ){
        //         $product->setPrice($product->getPrice()*100);
        //     }
        // });
        // $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event){
        //     //$form = $event->getForm();

        //     /** @var Product */
        //     $product = $event->getData();
            
        //     //dd($product->getPrice()/100);
        //     if($product && $product->getPrice() !== null ){
        //         $product->setPrice($product->getPrice()/100);
        //     }
        //     /*if($product === null){
        //         $form->add('category', EntityType::class, [
        //             'label' => 'Catégorie',
        //             'placeholder' => '-- Choisir une catégorie --',
        //             'class' => Category::class,
        //             'choice_label' => function(Category $category){
        //                 return strtoupper($category->getName());
        //             }
        //         ]);
        //     }*/
        // });
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
