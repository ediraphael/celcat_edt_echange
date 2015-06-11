<?php

namespace CelcatManagement\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ScheduleModificationType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user')
            ->add('canceled')
            ->add('validated')
            ->add('firstEvent')
            ->add('secondEvent')
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CelcatManagement\AppBundle\Entity\ScheduleModification'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'celcatmanagement_appbundle_schedulemodification';
    }
}
