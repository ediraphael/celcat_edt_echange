<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

use \Doctrine\Common\Collections\ArrayCollection;

class Event extends \ADesigns\CalendarBundle\Entity\EventEntity {

    private $room;
    private $category;
    private $day;
    private $week;
    private $module;
    private $professor;
    private $group;
    private $note;

    /**
     *
     * @var ArrayCollection 
     */
    private $formations;
    private $isDeleted = false;

    function __construct() {
        parent::__construct("", new \DateTime(), new \DateTime());
        $this->formations = new ArrayCollection();
    }

    public function getIsDeleted() {
        return $this->isDeleted;
    }

    public function isDeleted() {
        return $this->isDeleted;
    }

    public function delete() {
        $this->isDeleted = true;
    }

    public function undelete() {
        $this->isDeleted = false;
    }

    public function setIsDeleted($isDeleted) {
        $this->isDeleted = $isDeleted;
    }

    public function getFormations() {
        return $this->formations;
    }

    public function setFormations(ArrayCollection $formations) {
        $this->formations = $formations;
    }

    public function addFormation($formation) {
        if(is_array($formation) || $formation instanceof ArrayCollection) {
            foreach ($formation as $form) {
                if($form != '' && !$this->formations->contains($form)) {
                    $this->formations->add($form);
                }
            }
        }
        elseif ($formation != '' && !$this->formations->contains($formation)) {
            $this->formations->add($formation);
        }
    }

    public function removeFormation($formation) {
        $this->formations->removeElement($formation);
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

    /**
     * Convert calendar event details to an array
     * 
     * @return array $event 
     */
    public function toArray($canClick = true) {
        $event = parent::toArray();
        $event['room'] = $this->room;
        $event['category'] = $this->category;
        $event['module'] = $this->module;
        $event['professor'] = $this->professor;
        $event['group'] = $this->group;
        $event['note'] = $this->note;
        $event['formations'] = $this->formations->toArray();
        $event['day'] = $this->day;
        $event['week'] = $this->week;
        $event['canClick'] = $canClick;
        return $event;
    }

}
