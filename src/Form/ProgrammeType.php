<?php

namespace App\Form;

use App\Entity\Programme;
use App\Entity\Coach;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormEvent;


class ProgrammeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('type', ChoiceType::class, [
                
                'choices' => [
                    'Perte de poids' => 'Perte de poids',
                    'Prise de Masse' => 'Prise de Masse',
                    'Programme de flexibilité et de mobilité' => 'Programme de flexibilité et de mobilité',
                    'préparation physique spécifique au sport' => 'préparation physique spécifique au sport',

                ],
                'placeholder' => 'Choose a Type',
                
            ])
            ->add('duree', ChoiceType::class, [
                
                'choices' => [
                    '30 jour' => '30 jour',
                    '60 jour' => '60 jour',
                    '90 jour' => '90 jour',
                    '360 jour' => '360 jour',

                ],
                'placeholder' => 'Choose Program ',
                
            ])
            ->add('startdate', DateType::class, [
                'widget' => 'single_text', // Utilise un élément input de type date, permettant aux navigateurs de fournir un sélecteur de date
                'attr' => ['class' => 'my-date-class'], // Classe CSS personnalisée si nécessaire
                'label' => 'Date de PROGRAMME',
                // autres options...
            ])
            ->add('Coach', EntityType::class, [
                'class' => Coach::class,
                'choice_value' => 'id', // Specify which property of the object represents the value
                'choice_label' => 'name', // Specify which property of the object represents the label
                'placeholder' => 'Choose a coach',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Programme::class,
            'coaches' => null, // Set default value to null

        ]);
    }
    
    
    
}
