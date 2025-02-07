<?php

namespace App\Form;

use App\Entity\ModelTemplate;
use App\Entity\Section;
use App\Repository\ModelTemplateRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;

class SectionFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', null, [
                'label' => 'Nombre de la sección',
                'help' => 'Nombre que identifica a la sección entre las otras secciones',
            ])
            ->add('cssClass', null, [
                'label' => 'Clase css',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Descripción',
                'help' => 'Una descripción que diferencie a las otras secciones parecidas',
            ])
            ->add('identificador', TextType::class, [
                'help' => 'Opcional, para usar con funciones JS, identifica a la sección dentro de una página',
            ])
            ->add('disponible', CheckboxType::class, [
                'required' => false,
                'label' => '¿Disponible?',
                'label_attr' => ['class' => 'checkbox-custom text-dark'],
                'attr' => [
                    'class' => 'form-check-input ',
                ],
                'help' => 'Se mostrará la sección',
            ])
            ->add('disponibleAt', null, [
                'widget' => 'single_text',
                'label' => 'Inicia',
                'html5' => true,
                'required' => false,
//                'format' => 'yyyy-MM-dd HH:mm',
//                'attr' => ['class' => 'datetimepicker']
                'attr' => ['class' => 'form-control '],
                'input' => 'datetime',
            ])
            ->add('columns', IntegerType::class, [
                'label' => 'Cantidad de columnas',
                'required' => false,
            ])
            ->add('disponibleHastaAt', DateTimeType::class, [
                'widget' => 'single_text',
                'label' => 'Finaliza',
                'html5' => true,
                'required' => false,
                //                'format' => 'yyyy-MM-dd HH:mm',
                //                'attr' => ['class' => 'datetimepicker']
                'attr' => ['class' => 'form-control '],
                'input' => 'datetime',
            ])
            ->add('orden', IntegerType::class, [
                'label' => 'Orden en la página',
                'required' => false,
            ])
            ->add('modelTemplate', EntityType::class, [
                'class' => ModelTemplate::class,
                'query_builder' => fn(ModelTemplateRepository $er) => $er->findByTypeSection(),
                'help' => 'Opcional, llama a un template específico, debe estar en sections creado',
                'required' => false,
                'attr' => [
                    'class' => 'select2-enable',
                ],
            ])
            ->add('contenido', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'tinymce-editor form-control',
                ],
            ])
            ->add('imageFile', FileType::class, [
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Image([
                        'maxSize' => '2M',
                        'maxSizeMessage' => 'La imagen no debe superar los 2MB',
                        'mimeTypesMessage' => 'El archivo no es considerada una imagen',
                    ]),
                ],
                'attr' => [
                    'placeholder' => 'Ingrese una imagen para esta sección',
                ],
            ])
            ->add('title', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'tinymce-editor',
                ],
            ])
            ->add('footer', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'tinymce-editor form-control',
                ],
            ])
            ->add('isLinkExterno', CheckboxType::class, [
                'required' => false,
                'label' => false,
                'label_attr' => ['class' => 'checkbox-custom text-dark'],
                'attr' => [
                    'class' => 'form-check-input ',
                ],
            ])
            ->add('linkPosting', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ])
            ->add('linkRoute', TextType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                ],
            ]); // ; Final del builder
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Section::class,
        ]);
    }
}
