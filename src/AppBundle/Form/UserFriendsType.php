<?php
/**
 * Created by PhpStorm.
 * User: mad
 * Date: 16.09.17
 * Time: 22:27
 */

namespace AppBundle\Form;

use AppBundle\Entity\UserFriends;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class UserFriendsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*
            ->add('id', EntityType::class, array(
                'class' => 'AppBundle:User',
                /* //sort Users by username
                //http://symfony.com/doc/current/reference/forms/types/entity.html#using-a-custom-query-for-the-entities
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.username', 'ASC');
                },
                */
                /*
                //http://symfony.com/doc/current/reference/forms/types/entity.html#field-options
                'choice_label'  => function ($id) {

                    return $id->getId();
                }
                'choice_label' => 'id',
            ))*/
            ->add('id', NumberType::class)
            ->add('msg', TextType::class)
            ->add('submit', SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => UserFriends::class,
        ));
    }


}