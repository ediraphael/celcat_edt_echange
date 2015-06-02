<?php

namespace CelcatManagement\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class UserCalendarsType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('username',null,array(
                'label' => 'Nom d\'utilisateur'
            ))
            ->add('calendarFile', 'group_select', array(
                'label' => 'Nom fichier calendrier'
            ))
            ->add('calendarName', null, array(
                'label' => 'Nom calendrier'
            ))
            ->add('calendarComment', null, array(
                'label' => 'Commentaire'
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CelcatManagement\AppBundle\Entity\UserCalendars'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'celcatmanagement_appbundle_usercalendars';
    }
}
