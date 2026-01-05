<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType; // <--- Import Important
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Champ 1 : keywords (C'est lui qui posait problème)
            ->add('keywords', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['placeholder' => '🔍 Que cherchez-vous ?', 'class' => 'form-control']
            ])
            
            // Champ 2 : city
            ->add('city', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => ['placeholder' => '📍 Ville', 'class' => 'form-control']
            ])
            
            // Champ 3 : category
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => false,
                'required' => false,
                'placeholder' => '📂 Toutes les catégories',
                'attr' => ['class' => 'form-select']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
}