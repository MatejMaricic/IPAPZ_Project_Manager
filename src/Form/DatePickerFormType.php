<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/18/19
 * Time: 9:39 AM
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;

class DatePickerFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'date',
                DateType::class,
                [
                    'widget' => 'choice',
                ]
            );
    }
}
