<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

class Week
{
    
    private $tab_days,
            $description,
            $id,
            $tag,
            $date;
    
    function __construct()
    {
        $this->tab_days = array();
        $day = new Day();
        $day->setId(0);
        $day->setName("lundi");
        $this->tab_days[] = $day;
        $day = new Day();
        $day->setId(1);
        $day->setName("mardi");
        $this->tab_days[] = $day;
        $day = new Day();
        $day->setId(2);
        $day->setName("mercredi");
        $this->tab_days[] = $day;
        $day = new Day();
        $day->setId(3);
        $day->setName("jeudi");
        $this->tab_days[] = $day;
        $day = new Day();
        $day->setId(4);
        $day->setName("vendredi");
        $this->tab_days[] = $day;
        $day = new Day();
        $day->setId(5);
        $day->setName("samedi");
        $this->tab_days[] = $day;
        $this->description = "";
        $this->id = "";
        $this->tag = "";
        $this->date = "";
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
    
    public function addDay($day)
    {
        $this->tab_days[] = $day;
    }
    
    public function getDayById($day_id)
    {
        foreach ($this->tab_days as $day)
        {
            if($day->getId() == $day_id)
                return $day;
        }
        return null;
    }
    
    /**
     * 
     * @param type $day_name
     * @return type
     */
    public function getDayByName($day_name)
    {
        foreach ($this->tab_days as $day)
        {
            if($day->getName() == $day_name)
                return $day;
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
    public function getWeekFreeEventsList($start_time, $end_time, $formation_id)
    {
        $tab_free_events = array();
        foreach ($this->tab_days as $day)
        {
            //if(count($day->getTab_events()) > 0)
                $tab_free_events[$day->getName()] = $day->getFreeEventsList($start_time, $end_time, $formation_id);
        }
        return $tab_free_events;
    }


}