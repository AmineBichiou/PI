<?php

namespace App\Form;

use App\Entity\Entrepot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Positive;
use Symfony\Component\Validator\Constraints\Regex;

class EntrepotType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de l\'entrepôt',
                'attr' => [
                    'placeholder' => 'Exemple : Entrepôt Principal',
                ],
                'constraints' => [
                    new NotBlank(['message' => 'Le nom de l\'entrepôt ne peut pas être vide.']),
                    new Length([
                        'min' => 4,
                        'max' => 25,
                        'minMessage' => 'Le nom doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'Le nom ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('adresse', TextType::class, [
              'label' => 'Adresse',
              'attr' => [
                  'placeholder' => 'Exemple : belli village 8022',
              ],
              'constraints' => [
                  new NotBlank(['message' => 'L\'adresse ne peut pas être vide.']),
                  new Regex([
                      'pattern' => '/^[a-zA-ZÀ-ÿ\s]+\s\d{4,5}$/',
                      'message' => 'L\'adresse doit être sous la forme : "[nom de la rue ou du lieu] [code postal]". Exemple : belli village 8022',
                  ]),
              ],
          ])
        
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'attr' => [
                    'placeholder' => 'Exemple : Tunis',
                ],
                'required' => false,
                'constraints' => [
                    new NotBlank(['message' => 'Le ville de l\'entrepôt ne peut pas être vide.']),
                    new Length([
                        'min' => 4,
                        'max' => 100,
                        'minMessage' => 'La ville doit contenir au moins {{ limit }} caractères.',
                        'maxMessage' => 'La ville ne peut pas dépasser {{ limit }} caractères.',
                    ]),
                ],
            ])
            ->add('espace', NumberType::class, [
              'label' => 'Espace (en m)',
              'attr' => [
                    'placeholder' => 'Exemple : 150.75',
                ],
              'constraints' => [
                  new NotBlank(['message' => 'L\'espace ne peut pas être vide.']),
                  new Positive(['message' => 'L\'espace doit être un nombre positif.']),
                  new Regex([
                      'pattern' => '/^\d+(\.\d{1,2})?$/',
                      'message' => 'L\'espace doit être un nombre décimal avec au plus 2 chiffres après la virgule.',
                  ]),
              ],
          ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entrepot::class,
        ]);
    }
}