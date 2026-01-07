<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

// 👇 C'EST CES LIGNES QU'IL TE MANQUAIT 👇
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
// 👆 SANS ELLES, SYMFONY NE CONNAIT PAS LES RÈGLES DE SÉCURITÉ

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'Adresse Email', 'attr' => ['class' => 'form-control']])
            ->add('firstName', TextType::class, ['label' => 'Prénom', 'attr' => ['class' => 'form-control']])
            ->add('lastName', TextType::class, ['label' => 'Nom', 'attr' => ['class' => 'form-control']])
            ->add('phoneNumber', TextType::class, ['label' => 'Téléphone', 'required' => false, 'attr' => ['class' => 'form-control']])
            
            // 1. LE MOT DE PASSE ACTUEL (OBLIGATOIRE)
            ->add('currentPassword', PasswordType::class, [
                'label' => 'Mot de passe actuel (Requis pour valider)',
                'mapped' => false,
                'required' => true,
                'attr' => ['class' => 'form-control', 'autocomplete' => 'current-password'],
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez entrer votre mot de passe pour confirmer.']),
                    new UserPassword(['message' => 'Mot de passe incorrect.']), // C'est ici que ça bloquait
                ],
            ])

            // 2. LE NOUVEAU MOT DE PASSE (OPTIONNEL)
            ->add('newPassword', PasswordType::class, [
                'label' => 'Nouveau mot de passe (Laisser vide pour ne pas changer)',
                'mapped' => false,
                'required' => false,
                'attr' => ['class' => 'form-control', 'autocomplete' => 'new-password'],
                'constraints' => [
                    new Length(['min' => 6, 'minMessage' => 'Au moins 6 caractères']),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}