<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 18.02.19.
 * Time: 09:58
 */

namespace App\Form;



use App\Entity\Project;
use App\Entity\ProjectStatus;
use App\Repository\ProjectStatusRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectStatusFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
           ->add('name')

       ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProjectStatus::class
        ]);
    }
}