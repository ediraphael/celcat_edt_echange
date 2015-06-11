<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

use \Doctrine\Common\Collections\ArrayCollection;

class Event extends \ADesigns\CalendarBundle\Entity\EventEntity {

    /**
     *
     * @var string 
     */
    private $room;

    /**
     *
     * @var string 
     */
    private $category;

    /**
     *
     * @var string
     */
    private $day;

    /**
     *
     * @var string 
     */
    private $week;

    /**
     *
     * @var string 
     */
    private $module;

    /**
     *
     * @var ArrayCollection 
     */
    private $professors;

    /**
     *
     * @var group 
     */
    private $group;

    /**
     *
     * @var string 
     */
    private $note;

    /**
     *
     * @var ArrayCollection 
     */
    private $formations;

    /**
     *
     * @var boolean 
     */
    private $isDeleted = false;

    /**
     *
     * @var boolean 
     */
    private $isSwapable = false;

    /**
     *
     * @var boolean 
     */
    private $isEventSource = false;

    /**
     *
     * @var boolean
     */
    private $isClickable = false;

    /**
     *
     * @var boolean 
     */
    private $isSwaped = false;

    /**
     *
     * @var Event 
     */
    private $replacementEvent;

    function __construct() {
        parent::__construct("", new \DateTime(), new \DateTime());
        $this->formations = new ArrayCollection();
        $this->professors = new ArrayCollection();
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

    public function replaceBy(Event $replacementEvent) {
        if ($this->hasReplacementEvent()) {
            $this->replacementEvent->replaceBy($replacementEvent);
        } else {
            $this->replacementEvent = $replacementEvent;
        }
    }

    public function deleteReplacementEvent() {
        $this->replacementEvent = null;
    }

    public function hasReplacementEvent() {
        return $this->replacementEvent instanceof Event;
    }

    public function getReplacementEvent() {
        return $this->replacementEvent;
    }

    public function setReplacementEvent(Event $replacementEvent) {
        $this->replacementEvent = $replacementEvent;
        return $this;
    }

    public function getFormations() {
        return $this->formations;
    }

    public function setFormations(ArrayCollection $formations) {
        $this->formations = $formations;
    }

    public function addFormation($formation) {
        if (is_array($formation) || $formation instanceof ArrayCollection) {
            foreach ($formation as $form) {
                if ($form != '' && !$this->formations->contains($form)) {
                    $this->formations->add($form);
                }
            }
        } elseif ($formation != '' && !$this->formations->contains($formation)) {
            $this->formations->add($formation);
        }
    }

    public function removeFormation($formation) {
        $this->formations->removeElement($formation);
    }

    public function containsFormations($formations, $withoutProfessor = false) {
        if (is_array($formations) || $formations instanceof ArrayCollection) {
            $contains = false;
            foreach ($formations as $formation) {
                if ($formation[0] == 'g' || !$withoutProfessor) {
                    $contains = $contains || $this->formations->contains($formation);
                }
            }
            return $contains;
        } else {
            if ($formations[0] == 'g' || !$withoutProfessor) {
                return $this->formations->contains($formations);
            }
        }
        return false;
    }

    public function isEventCrossed(Event $event) {
        return $event->getStartDatetime() < $this->getEndDatetime() && $event->getEndDatetime() > $this->getStartDatetime();
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

    public function getStartDatetime() {
        if ($this->hasReplacementEvent()) {
            return $this->replacementEvent->getStartDatetime();
        } else {
            return $this->startDatetime;
        }
    }

    public function getEndDatetime() {
        if ($this->hasReplacementEvent()) {
            return $this->replacementEvent->getEndDatetime();
        } else {
            return $this->endDatetime;
        }
    }

    public function getRoom() {
        return $this->room;
    }

    public function getCategory() {
        return $this->category;
    }

    public function getDay() {
        if ($this->hasReplacementEvent()) {
            return $this->replacementEvent->day;
        }
        return $this->day;
    }

    public function getWeek() {
        if ($this->hasReplacementEvent()) {
            return $this->replacementEvent->week;
        }
        return $this->week;
    }

    public function getModule() {
        return $this->module;
    }

    public function getProfessors() {
        return $this->professors;
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

    public function setProfessors($professors) {
        $this->professors = $professors;
    }

    public function addProfessor($professor) {
        if (is_array($professor) || $professor instanceof ArrayCollection) {
            foreach ($professor as $prof) {
                if ($prof != '' && !$this->professors->contains($prof)) {
                    $this->professors->add($prof);
                }
            }
        } elseif ($professor != '' && !$this->professors->contains($professor)) {
            $this->professors->add($professor);
        }
    }

    public function removeProfessor($professor) {
        $this->professors->removeElement($professor);
    }

    public function containsProfessors($professors) {
        if (is_array($professors) || $professors instanceof ArrayCollection) {
            $contains = false;
            foreach ($professors as $professor) {
                $contains = $contains || $this->professors->contains($professor);
            }
            return $contains;
        } else {
            return $this->professors->contains($professors);
        }
    }

    public function setGroup($group) {
        $this->group = $group;
    }

    public function setNote($note) {
        $this->note = $note;
    }

    public function setBgColor($color) {
        if ($this->hasReplacementEvent()) {
            $this->replacementEvent->setBgColor($color);
        }
        parent::setBgColor($color);
    }

    public function getIsSwapable() {
        return $this->isSwapable;
    }

    public function setIsSwapable($isSwapable) {
        $this->isSwapable = $isSwapable;
        return $this;
    }

    public function swapable() {
        $this->isSwapable = true;
    }

    public function unswapable() {
        $this->isSwapable = false;
    }

    public function isSwapable() {
        return $this->isSwapable;
    }

    public function getIsEventSource() {
        return $this->isEventSource;
    }

    public function setIsEventSource($isEventSource) {
        $this->isEventSource = $isEventSource;
        return $this;
    }

    public function eventSource() {
        if ($this->hasReplacementEvent()) {
            $this->replacementEvent->eventSource();
        }
        $this->isEventSource = true;
    }

    public function unEventSource() {
        if ($this->hasReplacementEvent()) {
            $this->replacementEvent->unEventSource();
        }
        $this->isEventSource = false;
    }

    public function isEventSource() {
        return $this->isEventSource;
    }

    public function clickable() {
        $this->isClickable = true;
        return $this;
    }

    public function unclickable() {
        $this->isClickable = false;
        return $this;
    }

    public function isSwaped() {
        if ($this->hasReplacementEvent()) {
            return $this->replacementEvent->isSwaped();
        }
        return $this->isSwaped;
    }

    public function swaped() {
        $this->isSwaped = true;
    }

    public function unswaped() {
        $this->isSwaped = false;
    }

    public function canClick() {
        $todayDate = new \DateTime();
        return ($this->isClickable || $this->isSwapable || $this->isEventSource) && !($this->getStartDatetime() < $todayDate);
    }

    public function setCssClass($class) {
        if ($this->hasReplacementEvent()) {
            $this->replacementEvent->setCssClass($class);
        } else {
            parent::setCssClass($class);
        }
    }

    /**
     * Convert calendar event details to an array
     * 
     * @return array $event 
     */
    public function toArray() {
        if (!$this->hasReplacementEvent()) {
            $event = parent::toArray();
            $event['room'] = $this->room;
            $event['category'] = $this->category;
            $event['module'] = $this->module;
            $event['professors'] = $this->professors->toArray();
            $event['group'] = $this->group;
            $event['note'] = $this->note;
            $event['formations'] = $this->formations->toArray();
            $event['day'] = $this->day;
            $event['week'] = $this->week;
            $event['isSwapable'] = $this->isSwapable;
            $event['isClickable'] = $this->isClickable;
            $event['isEventSource'] = $this->isEventSource;
            $event['isSwaped'] = $this->isSwaped;
            $event['canClick'] = $this->canClick();
            $event['editable'] = $this->isEventSource;
        } else {
            $event = $this->replacementEvent->toArray();
        }
        return $event;
    }

}
