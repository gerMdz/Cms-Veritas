<?php

namespace App\Form;

use App\Entity\Entrada;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntradaComplexType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'titulo', TextareaType::class,
                [
                    'required' => true,
//                    'config' => [
//                        'uiColor' => '#ffffff',
//                        'language' => 'es',
//                        'input_sync' => true,
//                    ],
                    'label_attr' => [
                        'class' => 'text-primary',
                    ],
                    'help' => 'Título de la entrada, se muestra en pantalla',
                    'attr' => [
                        'required' => true,
                        'class' => 'input_title',
                        'rows' => 10,

                    ],
                ]
            )
            ->add(
                'contenido',
                TextareaType::class,
                [
                    'required' => false,
//                    'config' => [
//                        'uiColor' => '#ffffff',
//                    'toolbar' => 'full',
//                        'language' => 'es',
//                    ],
                    'label_attr' => [
                        'class' => 'text-primary',
                    ],
                    'help' => 'Contenido de la entrada, se muestra en pantalla',
                    'attr' => [
                        'required' => false,
                        'rows' => 10,
                        'class' => 'input_content',
                    ],
                ]
            ); // ; Final Builder
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Entrada::class,
        ]);
    }
}
