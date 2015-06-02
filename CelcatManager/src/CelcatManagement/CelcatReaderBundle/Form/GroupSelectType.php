<?php

namespace CelcatManagement\CelcatReaderBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;


class GroupSelectType extends AbstractType {

    private $groupManager;
    private $choices;

    public function __construct($celcatUrl, $celcatStudentPath, $celcatGroupIndex) {
        $groupManager = new \CelcatManagement\CelcatReaderBundle\Models\GroupManager();
        $url = $celcatUrl.$celcatStudentPath.$celcatGroupIndex;
        $groupManager->loadGroups($url);
        $this->groupManager = $groupManager;
        $this->choices = array();
        /* @var $group \CelcatManagement\CelcatReaderBundle\Models\Group */
        foreach ($groupManager->getGroupList() as $group) {
            $this->choices[$group->getXmlFile()] = $group;
        }
        
    }

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $transformer = new DataTransformer\GroupToXMLFileTransformer($this->groupManager);
        $builder->addModelTransformer($transformer);
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver) {
        $resolver->setDefaults(array(
            "choices" => $this->choices,
        ));
    }

    public function getParent() {
        return 'genemu_jqueryselect2_choice';
    }

    public function getName() {
        return 'group_select';
    }

}
