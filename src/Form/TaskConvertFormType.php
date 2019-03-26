<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 3/14/19
 * Time: 12:01 PM
 */

namespace App\Form;

use App\Entity\ProjectStatus;
use App\Entity\Task;
use App\Repository\ProjectStatusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskConvertFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $id = $options['project_id'];
        $builder
            ->add(
                'status',
                EntityType::class,
                [
                    'class' => ProjectStatus::class,
                    'choice_label' => 'name',
                    'query_builder' => function (ProjectStatusRepository $projectStatusRepository) use ($id) {
                        return $projectStatusRepository->findAllForProject($id);
                    }
                ]
            )
            ->add(
                'images',
                FileType::class,
                [
                    'required' => false,
                    'multiple' => true
                ]
            )
            ->add(
                'priority',
                ChoiceType::class,
                [
                    'choices' => [
                        'High' => 'High',
                        'Medium' => 'Medium',
                        'Low' => 'Low'
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Task::class,
                'project_id' => null
            ]
        );
    }
}
