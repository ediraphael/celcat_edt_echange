<?php

namespace CelcatManagement\AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class EmailType extends AbstractType {

    /**
     * Pre fix fromAddress
     * @var string 
     */
    private $fromAddress;

    /**
     * Pre fix toAddress
     * @var string 
     */
    private $toAddress;

    public function __construct($fromAddress = '', $toAddress = '') {
        $this->fromAddress = $fromAddress;
        $this->toAddress = $toAddress;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
                ->add('fromAddress', 'email', array(
                    'label' => 'De',
                    'data' => $this->fromAddress,
                    'read_only' => ($this->fromAddress != ''),
                    'widget_addon_prepend' => array(
                        'text' => '@',
                    )
                ))
                ->add('toAddress', 'email', array(
                    'label' => 'À',
                    'data' => $this->toAddress,
                    'read_only' => ($this->toAddress != ''),
                    'widget_addon_prepend' => array(
                        'text' => '@',
                    )
                ))
                ->add('subject', 'text', array(
                    'label' => 'sujet',
                ))
                ->add('body', 'ckeditor', array(
                    'attr' => array('rows' => 20),
                    'label' => 'message',
                ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function getName() {
        return 'mailer_email';
    }

}
