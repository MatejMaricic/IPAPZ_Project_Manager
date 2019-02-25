<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 16.02.19.
 * Time: 15:44
 */

namespace App\Form;

use App\Entity\Project;
use App\Entity\ProjectStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpParser\Node\Expr\Array_;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class ProjectFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name of your new project'

            ])
            ->add('deadline', DateType::class, [
                'label' => 'Deadline for project',
                'data' => new \DateTime()
            ])


            ->add('projectStatus',CollectionType::class,[
                    'label' =>false,
                    'entry_type' => ProjectStatusFormType::class,
                    'allow_add' => true,
                    'by_reference' => false,
                    'delete_empty' =>true
                ]

            )
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class
        ]);
    }
}