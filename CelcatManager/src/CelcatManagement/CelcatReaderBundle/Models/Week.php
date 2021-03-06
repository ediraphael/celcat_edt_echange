<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

class Week {

    /**
     *
     * @var Day[] 
     */
    private $arrayDays;
    private $description;
    private $id;
    private $tag;
    private $date;
    private $weeknumber;

    function __construct() {
        $this->arrayDays = array();
        $monday = new Day();
        $monday->setId(0);
        $monday->setName("lundi");
        $this->arrayDays[0] = $monday;
        $tuesday = new Day();
        $tuesday->setId(1);
        $tuesday->setName("mardi");
        $this->arrayDays[1] = $tuesday;
        $wednesday = new Day();
        $wednesday->setId(2);
        $wednesday->setName("mercredi");
        $this->arrayDays[2] = $wednesday;
        $thursday = new Day();
        $thursday->setId(3);
        $thursday->setName("jeudi");
        $this->arrayDays[3] = $thursday;
        $friday = new Day();
        $friday->setId(4);
        $friday->setName("vendredi");
        $this->arrayDays[4] = $friday;
        $saturday = new Day();
        $saturday->setId(5);
        $saturday->setName("samedi");
        $this->arrayDays[5] = $saturday;
        $sunday = new Day();
        $sunday->setId(6);
        $sunday->setName("dimanche");
        $this->arrayDays[6] = $sunday;
    }

    public function getArrayDays() {
        return $this->arrayDays;
    }

    public function getDescription() {
        return $this->description;
    }

    public function getId() {
        return $this->id;
    }

    public function getTag() {
        return $this->tag;
    }

    public function getDate() {
        return $this->date;
    }

    public function setArrayDays($arrayDays) {
        $this->arrayDays = $arrayDays;
    }

    public function setDescription($description) {
        $this->description = $description;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setTag($tag) {
        $this->tag = $tag;
    }

    public function setDate($date) {
        $this->date = $date;
    }

    public function addDay($day) {
        $this->arrayDays[] = $day;
    }

    /**
     * 
     * @param type $dayId
     * @return Day|null
     */
    public function getDayById($dayId) {
        if(isset($this->arrayDays[$dayId]) && $this->arrayDays != '') {
            return $this->arrayDays[$dayId];
        }
        return null;
    }

    /**
     * 
     * @param type $day_name
     * @return type
     */
    public function getDayByName($day_name) {
        foreach ($this->arrayDays as $day) {
            if ($day->getName() == $day_name) {
                return $day;
            }
        }
        return null;
    }

    /**
     * 
     * @param type $start_time
     * @param type $end_time
     * @param type $formation_id
     * @return type
     */
    public function getWeekFreeEventsList($start_time, $end_time) {
        $tab_free_events = array();
        foreach ($this->arrayDays as $day) {
            //if(count($day->getTab_events()) > 0)
            $tab_free_events[$day->getName()] = $day->getFreeEventsList($start_time, $end_time);
        }
        return $tab_free_events;
    }
    
    public function getWeeknumber() {
        return $this->weeknumber;
    }

    public function setWeeknumber($weeknumber) {
        $this->weeknumber = $weeknumber;
        return $this;
    }

}
