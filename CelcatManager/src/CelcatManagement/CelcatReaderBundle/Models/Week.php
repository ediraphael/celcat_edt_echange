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
    
    public function addDay(Day $day)
    {
        $this->tab_days[] = $day;
    }


}