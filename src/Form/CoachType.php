<?php

namespace App\Form;

use App\Entity\Coach;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Component\Form\DataTransformer\StringToFileTransformer;
use Symfony\Component\Form\DataTransformer;
use Symfony\Component\Validator\Constraints\File;

class CoachType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('email')
            ->add('img', FileType::class, [
                'label' => 'Your image (JPEG file)',
                'required' => true, // Ensure that the field is required
                'constraints' => [
                    new Assert\NotNull([ // Ensure that the file is not null
                        'message' => 'Please upload an image file.',
                    ]),
                    new Assert\File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Please upload a valid photo (JPEG or PNG).',
                    ]),
                ],
                'data_class' => null
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Coach::class,
        ]);
    }
}
