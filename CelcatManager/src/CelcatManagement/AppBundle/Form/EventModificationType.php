<?php

namespace CelcatManagement\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EventModificationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('eventId')
            ->add('eventTitre')
            ->add('professors')
            ->add('groupes')
            ->add('dateInitial')
            ->add('dateFinal')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CelcatManagement\AppBundle\Entity\EventModification'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'celcatmanagement_appbundle_eventmodification';
    }
}
