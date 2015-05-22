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
    
    public function getEventByIdAndByFormation($event_id, $formation_id)
    {
        foreach ($this->tab_events[$formation_id] as $event)
        {
            if($event->getId() == $event_id)
                return $event;
        }
        return null;
    }
    
    private function getDifferenceOfTime($start_time, $end_time)
    {
        $end=strtotime($end_time);
        $start=strtotime($start_time);
        return gmdate('H:i',$end-$start);
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
        $tab_busy_events = array();
        $tab_free_events = array();
        foreach ($this->tab_events[$formation_id] as $index => $event)
        {
//            if(count($this->tab_events[$formation_id])-1 > index)
//            {
//                $next_event = $this->tab_events[$formation_id][index + 1];
//                if($start_time >= $event->getEnd_time() && $end_time <= $next_event->getStart_time())
//                {
//                    $free_event = new Event();
//                    $free_event->setStart_time($start_time);
//                    $free_event->setEnd_time($end_time);
//                    $tab_free_events[] = $free_event;
//                }
//            }  
            $tab_temp = array();
            $tab_temp[] = $event->getStart_time();
            $tab_temp[] = $event->getEnd_time();
            $tab_busy_events[] = $tab_temp;
        }
//        return $tab_busy_events;
        foreach ($tab_busy_events as $index => $tab_time)
        {
            $free_duration_between_two_events = $this->getDifferenceOfTime($tab_time[0], $tab_time[1]);
            $wanted_duration = $this->getDifferenceOfTime($start_time, $end_time);
            if(count($tab_busy_events)-1 > $index)
            {
                $next_tab_time = $tab_busy_events[$index + 1];
                $free_duration_between_two_events = $this->getDifferenceOfTime($tab_time[1], $next_tab_time[0]);
                if($free_duration_between_two_events >= $wanted_duration)
                {
                    $temp_tab_free_duration = array();
                    $temp_tab_free_duration[] = $tab_time[1];
                    $temp_tab_free_duration[] = $next_tab_time[0];
                    $tab_free_events[] = $temp_tab_free_duration;
                }
            }
            else
            {
                if($tab_time[1] < "20h")
                {
                    $temp_tab_free_duration = array();
                    $temp_tab_free_duration[] = $tab_time[1];
                    $temp_tab_free_duration[] = "20:00";
                    $tab_free_events[] = $temp_tab_free_duration;
                }
            }
        }
        
        return $tab_free_events;
    }


}
