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
    
    public function addEvent($event)
    {
//        le créneau est ajouté dans un tableau via un index qui représente l'd de la formation
        if(!isset($this->tab_events[$event->getFormation()]))
            $this->tab_events[$event->getFormation()] = array();
        array_push($this->tab_events[$event->getFormation()], $event);
    }
    
    public function canAddEvent($start_time, $end_time, $formation_id)
    {
        foreach ($this->tab_events[$formation_id] as $event)
        {
            if(($start_time >= $event->getStart_time() && $start_time <= $event->getEnd_time()) || 
                        ($end_time >= $event->getStart_time() && $end_time <= $event->getEnd_time()))
                return false;   
        }
        return true;
    }
    
    
    public function getFreeEventsList($start_time, $end_time, $formation_id)
    {
        $tab_free_events = array();
        foreach ($this->tab_events[$formation_id] as $index => $event)
        {
            if(count($this->tab_events[$formation_id])-1 > index)
            {
                $next_event = $this->tab_events[$formation_id][index + 1];
                if($start_time >= $event->getEnd_time() && $end_time <= $next_event->getStart_time())
                {
                    $free_event = new Event();
                    $free_event->setStart_time($start_time);
                    $free_event->setEnd_time($end_time);
                    $tab_free_events[] = $free_event;
                }
            }    
        }
        return $tab_free_events;
    }


}
