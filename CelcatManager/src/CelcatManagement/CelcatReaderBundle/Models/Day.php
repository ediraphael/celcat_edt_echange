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
            $tab_temp = array();
            $tab_temp[] = $event->getStart_time();
            $tab_temp[] = $event->getEnd_time();
            $tab_busy_events[] = $tab_temp;
        }
        if(count($tab_busy_events) == 0)
        {
            $temp_tab_free_duration = array();
            $temp_tab_free_duration[] = "08:00";
            $temp_tab_free_duration[] = "20:00";
            $tab_free_events[] = $temp_tab_free_duration;
        }
        else
        {
    //        return $tab_busy_events;
            foreach ($tab_busy_events as $index => $tab_time)
            {
                $var_start_time =null;
                $var_end_time = null;
                $free_duration_between_two_events = $this->getDifferenceOfTime($tab_time[0], $tab_time[1]);
                $wanted_duration = $this->getDifferenceOfTime($start_time, $end_time);
                if($index == 0 && $this->getDifferenceOfTime("08:00", $tab_time[0]) >= $wanted_duration)
                {
                    $var_start_time = "08:00";
                    $var_end_time = $tab_time[0];
                    $this->insertFreeEvent($tab_free_events, $var_start_time, $var_end_time, $wanted_duration);
                }
                if(count($tab_busy_events)-1 > $index)
                {
                    $next_tab_time = $tab_busy_events[$index + 1];
    //                dans le cas ou les créneaux se chevauche (plusieurs créneaux en même temps)
                    if($tab_time[1] >= $next_tab_time[0] || $this->getDifferenceOfTime($tab_time[1], "20:00") < $wanted_duration)
                        continue;
                    $free_duration_between_two_events = $this->getDifferenceOfTime($tab_time[1], $next_tab_time[0]);
                    if($free_duration_between_two_events >= $wanted_duration)
                    {
                        $var_start_time = $tab_time[1];
                        $var_end_time = $next_tab_time[0];
                    }
                }
                else
                {
                    if($this->getDifferenceOfTime($tab_time[1], "20:00") >= $wanted_duration)
                    {
                        $var_start_time = $tab_time[1];
                        $var_end_time = "20:00";
                    }
                }
                $this->insertFreeEvent($tab_free_events, $var_start_time, $var_end_time, $wanted_duration);
            }
        }
        $this->getSubEvents($tab_free_events, $wanted_duration);
        return $tab_free_events;
    }
    
    
    private function getSubEvents(&$tab_free_events, $wanted_duration)
    {
        $temp_tab_free_events = array();
        foreach ($tab_free_events as $interval)
        {
            if($this->getDifferenceOfTime($interval[0], $interval[1]) > $wanted_duration)
            {
                $tab_temp = array();
                $hours = explode(":", $wanted_duration)[0];
                $minutes = explode(":", $wanted_duration)[1];
                $convert = strtotime("+$hours hours", strtotime($interval[0]));
                $convert = strtotime("+$minutes minutes", $convert);
                $calculated = date('H:i', $convert);
                $this->insertFreeEvent($temp_tab_free_events, $interval[0], $calculated, $wanted_duration);
                $calculated = date('H:i', strtotime("+1 hours", strtotime($interval[0])));
                while($calculated < $interval[1])
                {
                    $old_calculated = $calculated;
                    $hours = explode(":", $wanted_duration)[0];
                    $minutes = explode(":", $wanted_duration)[1];
                    $convert = strtotime("+$hours hours", strtotime($calculated));
                    $convert = strtotime("+$minutes minutes", $convert);
                    $calculated = date('H:i', $convert);
                    $this->insertFreeEvent($temp_tab_free_events, $old_calculated, $calculated, $wanted_duration);
                    $calculated = date('H:i', strtotime("+1 hours", strtotime($old_calculated)));
                }
            }
            else
                $this->insertFreeEvent($temp_tab_free_events, $interval[0], $interval[1], $wanted_duration);
        }
        $tab_free_events = $temp_tab_free_events;
    }
    
    
    private function insertFreeEvent(&$tab_free_events, $var_start_time, $var_end_time, $wanted_duration)
    {
        if($var_start_time != null && $var_end_time != null)
        {
            $temp_tab_free_duration = array();
            if($var_end_time > "12:30" && $var_start_time < "12:30")
            {
                if($this->getDifferenceOfTime($var_start_time, "12:30") >= $wanted_duration)
                {
                    $temp_tab_free_duration[] = $var_start_time;
                    $temp_tab_free_duration[] = "12:30";
                    $tab_free_events[] = $temp_tab_free_duration;
                }
                if($this->getDifferenceOfTime("13:30", $var_end_time) >= $wanted_duration && $var_end_time > "13:30")
                {
                    $temp_tab_free_duration[] = "13:30";
                    $temp_tab_free_duration[] = $var_end_time;
                    $tab_free_events[] = $temp_tab_free_duration;
                }
            }
            else
            {
                $temp_tab_free_duration[] = $var_start_time;
                $temp_tab_free_duration[] = $var_end_time;
                $tab_free_events[] = $temp_tab_free_duration;
            }
        }
    }


}