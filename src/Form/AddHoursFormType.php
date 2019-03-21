<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/15/19
 * Time: 8:23 AM
 */

namespace App\Form;

use App\Entity\HoursOnTask;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddHoursFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('hours', IntegerType::class)
            ->add(
                'message',
                TextareaType::class,
                [
                    'required' => false,
                ]
            )
            ->add(
                'billable',
                ChoiceType::class,
                [
                    'choices' => [
                        'Billable' => true,
                        'Not Billable' => false
                    ],
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => HoursOnTask::class
            ]
        );
    }
}
