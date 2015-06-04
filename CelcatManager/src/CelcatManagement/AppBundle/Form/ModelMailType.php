<?php

namespace CelcatManagement\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ModelMailType extends MailType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name','text', array(
                'label' => 'Nom'
            ))
        ;
        parent::buildForm($builder, $options);
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'CelcatManagement\AppBundle\Entity\ModelMail'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'celcatmanagement_appbundle_modelmail';
    }
}
