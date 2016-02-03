<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

/**
 * Description of UserType
 *
 * @author reda
 */


class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('username', null, array(
                    'required' => true,
                    ))
                ->add('email', EmailType::class, array(
                    'required' => true,
                    ))
                ->add('password', PasswordType::class, array(
                    'required' => true,
                    ))
                ->add('confirmPassword', PasswordType::class, array(
                    'mapped' => false, 
                    'required' => true,
                    ))
                ->add('avatarUrl', FileType::class, array(
                    'required' => false,
                ))
                ->add('save', SubmitType::class)
            ;
    }
    
    
    public function configureOptions(OptionsResolver $resolver) 
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\User',
        ));
    }

}
