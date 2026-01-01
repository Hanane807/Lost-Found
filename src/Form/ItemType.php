<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Item;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class ItemType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre'])
            ->add('description', TextareaType::class, ['label' => 'Description'])
            ->add('city', TextType::class, ['label' => 'Ville'])
            ->add('color', TextType::class, ['label' => 'Couleur', 'required' => false])
            ->add('keywords', TextType::class, ['label' => 'Mots clés', 'required' => false])
            ->add('dateLost', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Date'
            ])
            ->add('status', ChoiceType::class, [
                'choices'  => [
                    'Perdu' => 'lost',
                    'Trouvé' => 'found',
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            // Le champ imageFile !
            ->add('imageFile', FileType::class, [
                'label' => 'Photo',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }
}