<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

class Event
{

    private $id, 
            $colour, 
            $start_time, 
            $end_time, 
            $room, 
            $category, 
            $day, 
            $week, 
            $module,
            $professor, 
            $group, 
            $note;
    

    function __construct() 
    {
        $this->id = ""; 
        $this->colour = ""; 
        $this->start_time = ""; 
        $this->end_time = ""; 
        $this->room = ""; 
        $this->category = ""; 
        $this->day = ""; 
        $this->week = ""; 
        $this->module = ""; 
        $this->professor = ""; 
        $this->group = ""; 
        $this->note = ""; 
    }

    public function getId() {
        return $this->id;
    }

    public function getColour() {
        return $this->colour;
    }

    public function getStart_time() {
        return $this->start_time;
    }

    public function getEnd_time() {
        return $this->end_time;
    }

    public function getRoom() {
        return $this->room;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getDay() {
        return $this->day;
    }

    public function getWeek() {
        return $this->week;
    }

    public function getModule() {
        return $this->module;
    }

    public function getProfessor() {
        return $this->professor;
    }

    public function getGroup() {
        return $this->group;
    }

    public function getNote() {
        return $this->note;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setColour($colour) {
        $this->colour = $colour;
    }

    public function setStart_time($start_time) {
        $this->start_time = $start_time;
    }

    public function setEnd_time($end_time) {
        $this->end_time = $end_time;
    }

    public function setRoom($room) {
        $this->room = $room;
    }

    public function setCategory($category) {
        $this->category = $category;
    }

    public function setDay($day) {
        $this->day = $day;
    }

    public function setWeek($week) {
        $this->week = $week;
    }

    public function setModule($module) {
        $this->module = $module;
    }

    public function setProfessor($professor) {
        $this->professor = $professor;
    }

    public function setGroup($group) {
        $this->group = $group;
    }

    public function setNote($note) {
        $this->note = $note;
    }
}
