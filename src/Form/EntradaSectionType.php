<?php

namespace App\Form;

use App\Entity\Entrada;
use App\Entity\Section;
use App\Repository\SectionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntradaSectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('section', EntityType::class, [
                'class' => Section::class,
                'mapped' => false,
                'placeholder' => 'Seleccione sección',
                'label' => 'Secciones disponibles',
                'query_builder' => fn (SectionRepository $er) => $er->findDisponible(),
                'attr' => [
                    'class' => 'select2-modal',
                ],
            ])
            ->add('save', SubmitType::class, ['label' => 'Agregar', 'attr' => ['class' => 'btn btn-primary btn--pill']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entrada::class,
        ]);
    }
}
