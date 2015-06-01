<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

use \DOMDocument;

/**
 * Description of GroupeManager
 *
 * @author raphael
 */
class GroupManager {

    /**
     * Liste des groupes disponible
     * @var Group[] 
     */
    private $groupList;

    public function __construct() {
        $this->groupList = array();
    }

    /**
     * Fonction permettant de charger une liste de groupe depuis un URL celcal
     * exemple : http://celcat.univ-angers.fr/web/publi/etu/gindex.html
     * @param string $url
     */
    public function loadGroups($url = '') {
        if ($url != '') {
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTMLFile($url);
            $selects = $dom->getElementsByTagName('select');
            /* @var $selects \DOMNodeList */
            foreach ($selects as $select) {
                /* @var $select \DOMNode */
                $options = $select->childNodes;
                foreach ($options as $option) {
                    /* @var $option \DOMNode */
                    if ($option->attributes->getNamedItem('value')) {
                        $group = new Group();
                        $group->setName($option->nodeValue);
                        $group->setXmlFile(str_replace('.html', '.xml', $option->attributes->getNamedItem('value')->nodeValue));
                        $this->groupList[] = $group;
                    }
                }
            }
        }
    }

    function getGroupList() {
        return $this->groupList;
    }

    function setGroupList(array $groupList) {
        $this->groupList = $groupList;
    }
}
