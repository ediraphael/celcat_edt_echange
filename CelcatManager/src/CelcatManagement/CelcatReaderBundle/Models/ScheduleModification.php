<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

class ScheduleModification {

    /**
     *
     * @var Event 
     */
    private $firstEvent;

    /**
     *
     * @var Event 
     */
    private $secondEvent;

    /**
     * swap, resize, drop
     * @var string 
     */
    private $modificationType;

    /**
     * Function to set a swap modification
     * @param \CelcatManagement\CelcatReaderBundle\Models\Event $firstEvent Original event, with his modification in his replacementEvent attribut
     * @param \CelcatManagement\CelcatReaderBundle\Models\Event $secondEvent Original event, with his modification in his replacementEvent attribut
     */
    public function setSwapModification(Event $firstEvent, Event $secondEvent) {
        $this->firstEvent = $firstEvent;
        $this->secondEvent = $secondEvent;
        $this->modificationType = 'swap';
    }

    /**
     * 
     * @return boolean
     */
    public function isSwapModification() {
        return $this->modificationType == 'swap';
    }

    /**
     * Fonction to set a resize modification
     * @param \CelcatManagement\CelcatReaderBundle\Models\Event $event Original event, with his modification in his replacementEvent attribut
     */
    public function setResizeModification(Event $event) {
        $this->firstEvent = $event;
        $this->secondEvent = null;
        $this->modificationType = 'resize';
    }

    /**
     * 
     * @return boolean
     */
    public function isResizeModification() {
        return $this->modificationType == 'resize';
    }

    /**
     * Fonction to set a drop modification
     * @param \CelcatManagement\CelcatReaderBundle\Models\Event $event Original event, with his modification in his replacementEvent attribut
     */
    public function setDropModification(Event $event) {
        $this->firstEvent = $event;
        $this->secondEvent = null;
        $this->modificationType = 'drop';
    }

    /**
     * 
     * @return boolean
     */
    public function isDropModification() {
        return $this->modificationType == 'drop';
    }

    public function getFirstEvent() {
        return $this->firstEvent;
    }

    public function getSecondEvent() {
        return $this->secondEvent;
    }

    public function getModificationType() {
        return $this->modificationType;
    }

    public function setFirstEvent(Event $firstEvent) {
        $this->firstEvent = $firstEvent;
        return $this;
    }

    public function setSecondEvent(Event $secondEvent) {
        $this->secondEvent = $secondEvent;
        return $this;
    }

    public function toArray() {
        $array = array();
        $array['firstEvent'] = $this->firstEvent->toArray();
        if ($this->secondEvent != null && $this->secondEvent != '') {
            $array['secondEvent'] = $this->secondEvent->toArray();
        } else {
            $array['secondEvent'] = '';
        }
        $array['modificationType'] = $this->modificationType;

        return $array;
    }

}
