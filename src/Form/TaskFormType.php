<?php
/**
 * Created by PhpStorm.
 * User: ipa
 * Date: 19.02.19.
 * Time: 19:08
 */

namespace App\Form;


use App\Entity\ProjectStatus;
use App\Entity\Task;
use App\Repository\ProjectStatusRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $id = $options['project_id'];
        $builder
            ->add('name', TextType::class)
            ->add('content', TextareaType::class)
            ->add('status', EntityType::class,[
                'class' => ProjectStatus::class,
                'choice_label' => 'name',
                'query_builder' => function(ProjectStatusRepository $projectStatusRepository) use ($id){
                    return $projectStatusRepository->findAllForProject($id);
                }
            ])
            ->add('images', FileType::class,[
                'required' => false,
                'multiple' => true
            ])
        ;

    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
            'project_id' => null
        ]);
    }
}