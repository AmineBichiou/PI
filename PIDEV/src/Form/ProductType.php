<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Produit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Positive;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du produit',
                'constraints' => [
                    new NotBlank(['message' => 'Le nom du produit ne peut pas être vide.'])
                ],
            ])
            ->add('prixUnitaire', MoneyType::class, [
                'label' => 'Prix Unitaire',
                'currency' => 'TND', // Utilise "EUR", "USD" ou autre si nécessaire
                'constraints' => [
                    new NotBlank(['message' => 'Le prix ne peut pas être vide.']),
                    new Positive(['message' => 'Le prix doit être un nombre positif.']),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank(['message' => 'La description ne peut pas être vide.'])
                ],
            ])
            ->add('urlImageProduit', FileType::class, [
                'label' => 'Image du produit',
                'mapped' => false, // Indique que ce champ n'est pas directement lié à l'entité
                'required' => !$options['is_edit'], // Si c'est une édition, le fichier peut être optionnel
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/gif'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (JPG, PNG, GIF).',
                    ])
                ],
                'attr' => ['accept' => 'image/*'],
            ])
            ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                'label' => 'Catégorie',
                'placeholder' => 'Sélectionner une catégorie',
                'constraints' => [
                    new NotBlank(['message' => 'Veuillez sélectionner une catégorie.'])
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer',
                'attr' => ['class' => 'btn btn-success'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
            'is_edit' => false, 
        ]);
    }
}
