<?php

namespace App\Form\Step\Entrada;

use App\Entity\Entrada;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StepThreeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('encabezado', CheckboxType::class, [
                    'required' => false,
                    'label' => false,
                    'label_attr' => ['class' => 'checkbox-custom text-dark'],
                    'help' => 'A futuro, aun no implementado',
                    'attr' => [
                        'class' => 'form-check-input ',
                    ],
                ]
            )
            ->add('destacado', CheckboxType::class, [
                    'required' => false,
                    'label' => false,
                    'label_attr' => ['class' => 'checkbox-custom text-dark'],
                    'help' => 'Acción especial, ej.: doble tamaño según el template de la entrada',
                    'attr' => [
                        'class' => 'form-check-input ',
                    ],
                ]
            )
            ->add('isSinTitulo', CheckboxType::class, [
                    'required' => false,
                    'label' => false,
                    'help' => 'Para omitir el título según el template',
                    'label_attr' => ['class' => 'checkbox-custom text-dark'],
                    'attr' => [
                        'class' => 'form-check-input ',
                    ],
                ]
            )
        ; // Fin del builder
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Entrada::class,
            ]
        );
    }
}
