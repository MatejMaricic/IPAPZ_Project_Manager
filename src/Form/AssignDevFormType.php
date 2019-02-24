<?php
/**
 * Created by PhpStorm.
 * User: matej
 * Date: 22.02.19.
 * Time: 13:38
 */

namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AssignDevFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $id = $options['id'];
        $builder
            ->add('users', EntityType::class, [
                'label'=> 'Choose Developers ',
                'class' => User::class,
                'choice_label' => 'email',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function(UserRepository $userRepository) use ($id){
                    return $userRepository->findAllDevelopers($id);
                }


            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'class' => User::class,
            'id' => array()
        ]);
    }
}