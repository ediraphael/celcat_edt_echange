<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

class Event extends \ADesigns\CalendarBundle\Entity\EventEntity {

    private $room,
            $category,
            $day,
            $week,
            $module,
            $professor,
            $group,
            $note,
            $formation;

    function __construct() {
        parent::__construct("", new \DateTime(), new \DateTime());
        $this->id = "";
        $this->room = "";
        $this->category = "";
        $this->day = "";
        $this->week = "";
        $this->module = "";
        $this->professor = "";
        $this->group = "";
        $this->note = "";
        $this->formation = "";
    }

    public function getFormation() {
        return $this->formation;
    }

    public function setFormation($formation) {
        $this->formation = $formation;
    }

    public function getId() {
        return $this->id;
    }

    public function getStartTime() {
        return $this->getStartDatetime()->format("H:i");
    }

    public function getEndTime() {
        return $this->getEndDatetime()->format("H:i");
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
