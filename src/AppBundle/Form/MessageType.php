<?php
/**
 * Created by PhpStorm.
 * User: mad
 * Date: 09.08.17
 * Time: 23:01
 */

namespace AppBundle\Form;

use AppBundle\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessageType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array('label' => 'Title', 'required'  => true))
            //->add('date', DateType::class, array('label' => ' ' ))
            //->add('author', TextType::class, array('label' => 'Author', 'required'   => true ))
            ->add('content', TextareaType::class, array(
                'label' => 'Text',
                'attr' => array('rows' => '2', 'cols' => '50'),
                ))
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
            'data_class' => Message::class,
        ));
    }

}