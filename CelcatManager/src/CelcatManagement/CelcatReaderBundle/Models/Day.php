<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

class Day
{
    
    private $tab_events,
            $id,
            $name;
    
    function __construct()
    {
        $this->tab_events = array();
        $this->name = "";
        $this->id = "";
    }
    
    public function getTab_events() {
        return $this->tab_events;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setTab_events($tab_events) {
        $this->tab_events = $tab_events;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }
    
    public function addEvent(Event $event)
    {
        $this->tab_events[] = $event;
    }


}
