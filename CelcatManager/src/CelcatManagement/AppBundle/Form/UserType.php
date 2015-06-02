<?php

namespace CelcatManagement\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserType extends AbstractType {

    private $username;
    
    function __construct($username) {
        $this->username = $username;
    }

    
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $principal = $builder->create('tabPrincipal', 'tab', array(
            'label' => 'Princpal',
            'icon' => '',
            'inherit_data' => true,
        ));
        $principal
                ->add('gidNumber','text',array(
                    'disabled' => true
                ))
                ->add('fullName', 'text',array(
                    'disabled' => true
                ))
                ->add('username', 'text',array(
                    'disabled' => true
                ))
                ->add('mail', 'text',array(
                    'disabled' => true
                ))
                ->add('group', 'text',array(
                    'disabled' => true
                ))
                ->add('groupName', 'text',array(
                    'disabled' => true
                ))
        ;
        $tabCalendar = $builder->create('tabCalendar', 'tab', array(
            'label' => 'Calendrier',
            'icon' => '',
            'inherit_data' => true,
        ));
        $tabCalendar->add('calendars', 'collection', array(
            'label' => 'Calendrier :',
            'type' => new UserCalendarsType($this->username),
            'allow_add' => true,
            'allow_delete' => true,
            'prototype' => true,
            'options' => array(
                'horizontal' => true,
                'label_render' => false,
                'horizontal_input_wrapper_class' => "col-lg-8",
            ),
        ));



        $builder->add($principal)
                ->add($tabCalendar)
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            'data_class' => 'CelcatManagement\AppBundle\Security\User'
        ));
    }

    /**
     * @return string
     */
    public function getName() {
        return 'celcatmanagement_appbundle_user';
    }

}
