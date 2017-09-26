<?php
/**
 * Created by PhpStorm.
 * User: mad
 * Date: 13.08.17
 * Time: 22:08
 */

namespace AppBundle\Form;


use AppBundle\Entity\Message;
use AppBundle\Entity\MyProduct;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            //->add('category', TextType::class)
            ->add('category', EntityType::class, array(
                // query choices from this entity -> all categories were listed automatically
                'class' => 'AppBundle:MyCategory',
                // property as the visible option string
                'choice_label' => 'name',
            ))
            ->add('name', TextType::class)
            ->add('price', MoneyType::class)
            ->add('description', TextType::class)
            ->add('image', FileType::class, array('label' => 'Product Image'))
            ->add('brochure', FileType::class, array('label' => 'Brochure (PDF)', 'required'  => false))
            ->add('submit', SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => MyProduct::class,
        ));
    }


}