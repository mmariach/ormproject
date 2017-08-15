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
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, array('required'  => true))
            ->add('price', NumberType::class)
            ->add('description', TextType::class)
            ->add('category', TextType::class)
            ->add('brochure', FileType::class, array('label' => 'Brochure (PDF file)'))
            ->add('submit', SubmitType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     * Every form needs to know the name of the class that holds the underlying data (e.g. .../Msg.php)
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => MyProduct::class,
        ));
    }


}