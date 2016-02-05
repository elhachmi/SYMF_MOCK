<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Description of UserType.
 *
 * @author reda
 */
class UserType extends AbstractType
{
    const USER_AVATAR_DIR = '/../web/uploads/avatars';

    public function buildForm(FormBuilderInterface $builder)
    {
        $builder
            ->add(
                'username',
                null,
                array(
                    'required' => true,
                    )
            )
            ->add(
                'email',
                EmailType::class,
                array(
                    'required' => true,
                    )
            )
            ->add(
                'password',
                RepeatedType::class,
                array(
                    'type' => PasswordType::class,
                    'required' => true,
                    'first_options' => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                    )
            )
            ->add(
                'avatarUrl',
                FileType::class,
                array(
                    'required' => false,
                    )
            )
            ->add('save', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            array(
            'data_class' => 'AppBundle\Entity\User',
            'allow_extra_fields' => true,
            )
        );
    }
}
