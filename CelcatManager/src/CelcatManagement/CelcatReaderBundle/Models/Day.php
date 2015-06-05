<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

class Day {

    /**
     *
     * @var Event[]
     */
    private $arrayEvents;
    private $id;
    private $name;

    function __construct() {
        $this->arrayEvents = array();
    }

    public function getArrayEvents() {
        return $this->arrayEvents;
    }

    public function getId() {
        return $this->id;
    }

    public function getName() {
        return $this->name;
    }

    public function setArrayEvents($arrayEvents) {
        $this->arrayEvents = $arrayEvents;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setName($name) {
        $this->name = $name;
    }

    /**
     * 
     * @param string $eventId
     * @param string $formationId
     * @return Event|null
     */
    public function getEventByIdAndByFormation($eventId, $formationId) {
        foreach ($this->arrayEvents as $event) {
            if ($event->getId() == $eventId && $event->getFormations()->contains($formationId)) {
                return $event;
            }
        }
        return null;
    }

    /**
     * 
     * @param string $eventId
     * @return Event|null
     */
    public function getEventById($eventId) {
        if (isset($this->arrayEvents[$eventId]) && $this->arrayEvents[$eventId]) {
            return $this->arrayEvents[$eventId];
        }
        return null;
    }

    /**
     * 
     * @param string $startTime
     * @param string $endTime
     * @return string
     */
    private function getDifferenceOfTime($startTime, $endTime) {
        $end = strtotime($endTime);
        $start = strtotime($startTime);
        return gmdate('H:i', $end - $start);
    }

    /**
     * 
     * @param Event $event
     */
    public function addEvent($event) {
        if (isset($this->arrayEvents[$event->getId()]) && $this->arrayEvents[$event->getId()] != '') {
            $this->arrayEvents[$event->getId()]->addFormation($event->getFormations());
        } else {
            $this->arrayEvents[$event->getId()] = $event;
        }
    }

    /**
     * 
     * @param string $eventId
     * @return boolean
     */
    public function removeEvent($eventId) {
        if (isset($this->arrayEvents[$eventId]) && $this->arrayEvents[$eventId]) {
            $this->arrayEvents[$eventId]->delete();
            return true;
        }
        return false;
    }
    
    
    public function getFormationEvents($formation_id)
    {
        $array_events = array();
        foreach ($this->arrayEvents as $event)
        {
            if($event->getFormations()->contains($formation_id))
            {
                $array_events[] = $event;
            }
        }
        return $array_events;
    }

    /**
     * 
     * @param type $eventId
     * @param type $startTime
     * @param type $endTime
     * @param type $formation_id
     * @return boolean
     */
    public function canAddEvent($eventId, $startTime, $endTime, $formation_id, $user) {
        if (isset($this->arrayEvents[$eventId]) && $this->arrayEvents[$eventId]) {
            $array_events = $this->getFormationEvents($formation_id);
            foreach ($array_events as $event)
            {
                if($eventId != $event->getId())
                {
                    if (count($event->getProfessors()) == 0 || 
                            ($startTime > $event->getStartTime() && $startTime < $event->getEndTime()) ||
                            ($endTime > $event->getStartTime() && $endTime < $event->getEndTime()) ||
                            ($startTime < $event->getStartTime() && $endTime > $event->getEndTime())) {
                        return false;
                    }
//                    if(!$user->calendarExists($event->getFormations()))
//                    {
//                        return false;
//                    }
                }
            }
        }

        return true;
    }
    
     

    /**
     * 
     * @param type $startTime
     * @param type $endTime
     * @param type $formation_id
     * @return array
     */
    public function getFreeEventsList($startTime, $endTime, $formation_id) {
        $tabBusEvents = array();
        $tabFreeEvents = array();
        $wantedDuration = $this->getDifferenceOfTime($startTime, $endTime);
        if (count($this->arrayEvents) > 0) {
            foreach ($this->arrayEvents as $index => $event) {
                $tabTemp = array();
                $tabTemp[] = $event->getStartTime();
                $tabTemp[] = $event->getEndTime();
                $tabBusEvents[] = $tabTemp;
            }
        }
        if (count($tabBusEvents) == 0) {
            $this->insertFreeEvent($tabFreeEvents, "08:00", "20:00", $wantedDuration);
        } else {
            //        return $tab_busy_events;
            foreach ($tabBusEvents as $index => $tabTime) {
                $var_start_time = null;
                $var_end_time = null;
                $free_duration_between_two_events = $this->getDifferenceOfTime($tabTime[0], $tabTime[1]);
                if ($index == 0 && $this->getDifferenceOfTime("08:00", $tabTime[0]) >= $wantedDuration) {
                    $var_start_time = "08:00";
                    $var_end_time = $tabTime[0];
                    $this->insertFreeEvent($tabFreeEvents, $var_start_time, $var_end_time, $wantedDuration);
                }
                if (count($tabBusEvents) - 1 > $index) {
                    $next_tab_time = $tabBusEvents[$index + 1];
                    //                dans le cas ou les créneaux se chevauche (plusieurs créneaux en même temps)
                    if ($tabTime[1] >= $next_tab_time[0] || $this->getDifferenceOfTime($tabTime[1], "20:00") < $wantedDuration) {
                        continue;
                    }
                    $free_duration_between_two_events = $this->getDifferenceOfTime($tabTime[1], $next_tab_time[0]);
                    if ($free_duration_between_two_events >= $wantedDuration) {
                        $var_start_time = $tabTime[1];
                        $var_end_time = $next_tab_time[0];
                    }
                } else {
                    if ($this->getDifferenceOfTime($tabTime[1], "20:00") >= $wantedDuration) {
                        $var_start_time = $tabTime[1];
                        $var_end_time = "20:00";
                    }
                }
                $this->insertFreeEvent($tabFreeEvents, $var_start_time, $var_end_time, $wantedDuration);
            }
        }
        $this->getSubEvents($tabFreeEvents, $wantedDuration);
        return $tabFreeEvents;
    }

    /**
     * 
     * @param type $tabFreeEvents
     * @param type $varStartTime
     * @param type $varEndTime
     * @param type $wantedDuration
     */
    private function insertFreeEvent(&$tabFreeEvents, $varStartTime, $varEndTime, $wantedDuration) {
        if ($varStartTime != null && $varEndTime != null) {
            $tempTabFreeDuration = array();
            if ($varEndTime > "12:30" && $varStartTime < "12:30") {
                if ($this->getDifferenceOfTime($varStartTime, "12:30") >= $wantedDuration) {
                    $tempTabFreeDuration[] = $varStartTime;
                    $tempTabFreeDuration[] = "12:30";
                    $tabFreeEvents[] = $tempTabFreeDuration;
                }
                if ($this->getDifferenceOfTime("13:30", $varEndTime) >= $wantedDuration && $varEndTime > "13:30") {
                    $tempTabFreeDuration = array();
                    $tempTabFreeDuration[] = "13:30";
                    $tempTabFreeDuration[] = $varEndTime;
                    $tabFreeEvents[] = $tempTabFreeDuration;
                }
            } else {
                $tempTabFreeDuration[] = $varStartTime;
                $tempTabFreeDuration[] = $varEndTime;
                $tabFreeEvents[] = $tempTabFreeDuration;
            }
        }
    }

    /**
     * 
     * @param type $tabFreeEvents
     * @param type $wantedDuration
     */
    private function getSubEvents(&$tabFreeEvents, $wantedDuration) {
        // $desired_additionel_time = "+1 hours";
        $desiredAdditionelTime = "+15 minutes";
        $tempTabFreeEvents = array();
        foreach ($tabFreeEvents as $interval) {
            if ($this->getDifferenceOfTime($interval[0], $interval[1]) > $wantedDuration) {
                $tab_temp = array();
                $hours = explode(":", $wantedDuration)[0];
                $minutes = explode(":", $wantedDuration)[1];
                $convert = strtotime("+$hours hours", strtotime($interval[0]));
                $convert = strtotime("+$minutes minutes", $convert);
                $calculated = date('H:i', $convert);
                $this->insertFreeEvent($tempTabFreeEvents, $interval[0], $calculated, $wantedDuration);
                $calculated = date('H:i', strtotime($desiredAdditionelTime, strtotime($interval[0])));
                while ($calculated < $interval[1]) {
                    $oldCalculated = $calculated;
                    $hours = explode(":", $wantedDuration)[0];
                    $minutes = explode(":", $wantedDuration)[1];
                    $convert = strtotime("+$hours hours", strtotime($calculated));
                    $convert = strtotime("+$minutes minutes", $convert);
                    $calculated = date('H:i', $convert);
                    if ($calculated > "20:00") {
                        break;
                    }
                    $this->insertFreeEvent($tempTabFreeEvents, $oldCalculated, $calculated, $wantedDuration);
                    $calculated = date('H:i', strtotime($desiredAdditionelTime, strtotime($oldCalculated)));
                }
            } else {
                $this->insertFreeEvent($tempTabFreeEvents, $interval[0], $interval[1], $wantedDuration);
            }
        }
        $tabFreeEvents = $tempTabFreeEvents;
    }

}
