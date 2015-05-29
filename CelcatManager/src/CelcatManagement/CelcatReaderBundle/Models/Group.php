<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

/**
 * Description of Group
 *
 * @author raphael
 */
class Group {
    /**
     * Nom du groupe
     * @var string 
     */
    private $name;
    /**
     * Nom du fichier xml du groupe
     * @var string 
     */
    private $XmlFile;
    
    function getName() {
        return $this->name;
    }

    function getXmlFile() {
        return $this->XmlFile;
    }

    function setName($name) {
        $this->name = $name;
    }

    function setXmlFile($XmlFile) {
        $this->XmlFile = $XmlFile;
    }
}
