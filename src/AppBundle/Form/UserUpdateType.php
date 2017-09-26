<?php
// src/AppBundle/Form/UserType.php
namespace AppBundle\Form;
/**
 * Created by PhpStorm.
 * User: mad
 * Date: 04.08.17
 * Time: 16:44
 */
use AppBundle\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

        
class UserUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('userAvatarFilename', FileType::class, array('label' => 'Avatar Image', 'required'  => false)) //solved problem with file type: see: https://stackoverflow.com/questions/14423265/symfony-2-form-exception-when-modifying-an-object-that-has-a-filepicture-fie
            ->add('plainPassword', PasswordType::class)
            ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
        ));
    }
}