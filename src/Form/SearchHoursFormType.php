<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/15/19
 * Time: 2:30 PM
 */

namespace App\Form;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchHoursFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $options['user'];
        $builder
            ->add(
                'project',
                EntityType::class,
                [
                    'label' => 'Choose Project ',
                    'class' => Project::class,
                    'choice_label' => 'name',
                    'multiple' => false,
                    'expanded' => false,
                    'query_builder' => function (ProjectRepository $projectRepository) use ($user) {
                        return $projectRepository->findByUserId($user->getId());
                    }
                ]
            )
            ->add(
                'date',
                DateType::class,
                [
                    'widget' => 'choice',
                ]
            )
            ->add(
                'billable',
                ChoiceType::class,
                [
                    'choices' => [
                        'Billable' => true,
                        'Not Billable' => false
                    ]]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'user' => array()
            ]
        );
    }
}
