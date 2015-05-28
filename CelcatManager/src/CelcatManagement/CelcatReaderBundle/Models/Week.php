<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

class Week {

    private $tab_days;
    private $description;
    private $id;
    private $tag;
    private $date;

    function __construct() {
        $this->tab_days = array();
        $monday = new Day();
        $monday->setId(0);
        $monday->setName("lundi");
        $this->tab_days[] = $monday;
        $tuesday = new Day();
        $tuesday->setId(1);
        $tuesday->setName("mardi");
        $this->tab_days[] = $tuesday;
        $wednesday = new Day();
        $wednesday->setId(2);
        $wednesday->setName("mercredi");
        $this->tab_days[] = $wednesday;
        $thursday = new Day();
        $thursday->setId(3);
        $thursday->setName("jeudi");
        $this->tab_days[] = $thursday;
        $friday = new Day();
        $friday->setId(4);
        $friday->setName("vendredi");
        $this->tab_days[] = $friday;
        $saturday = new Day();
        $saturday->setId(5);
        $saturday->setName("samedi");
        $this->tab_days[] = $saturday;
        $sunday = new Day();
        $sunday->setId(6);
        $sunday->setName("dimanche");
        $this->tab_days[] = $sunday;
    }

    public function getTab_days() {
        return $this->tab_days;
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

    public function setTab_days($tab_days) {
        $this->tab_days = $tab_days;
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
        $this->tab_days[] = $day;
    }

    public function getDayById($day_id) {
        foreach ($this->tab_days as $day) {
            if ($day->getId() == $day_id) {
                return $day;
            }
        }
        return null;
    }

    /**
     * 
     * @param type $day_name
     * @return type
     */
    public function getDayByName($day_name) {
        foreach ($this->tab_days as $day) {
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
    public function getWeekFreeEventsList($start_time, $end_time, $formation_id) {
        $tab_free_events = array();
        foreach ($this->tab_days as $day) {
            //if(count($day->getTab_events()) > 0)
            $tab_free_events[$day->getName()] = $day->getFreeEventsList($start_time, $end_time, $formation_id);
        }
        return $tab_free_events;
    }

}
