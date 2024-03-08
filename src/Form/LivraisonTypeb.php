<?php

namespace App\Form;

use App\Entity\Livraison;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Range;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class LivraisonTypeb extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('status', ChoiceType::class, [
            'choices' => [
                'Delivered' => 1,
                'Not Delivered' => 0,
            ],
            'placeholder' => 'Select status',
            // If you want to customize the appearance, you can add additional options
            'attr' => [
                'class' => 'form-control', // Add classes for styling if needed
            ],
        ]);
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livraison::class,
        ]);
    }
}
