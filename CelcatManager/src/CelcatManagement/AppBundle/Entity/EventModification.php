<?php

namespace CelcatManagement\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EventModification
 */
class EventModification
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $eventId;

    /**
     * @var string
     */
    private $eventTitre;

    /**
     * @var array
     */
    private $professors;

    /**
     * @var array
     */
    private $groupes;

    /**
     * @var \DateTime
     */
    private $startDateTimeInitial;

    /**
     * @var \DateTime
     */
    private $endDateTimeInitial;

    /**
     * @var \DateTime
     */
    private $startDateTimeFinal;

    /**
     * @var \DateTime
     */
    private $endDateTimeFinal;
    
    public function __construct() {
        $this->groupes = array();
        $this->professors = array();
        
    }
    
    public function feedByEvent(\CelcatManagement\CelcatReaderBundle\Models\Event $event) {
        $element = clone $event;
        $this->eventId = $element->getId();
        $this->eventTitre = $element->getTitle();
        $this->professors = $element->getProfessors()->toArray();
        $this->groupes = $element->getGroup();
        $this->startDateTimeFinal = $element->getStartDatetime();
        $this->endDateTimeFinal = $element->getEndDatetime();
        $element->deleteReplacementEvent();
        $this->startDateTimeInitial = $element->getStartDatetime();
        $this->endDateTimeInitial = $element->getEndDatetime();
    }

    
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set eventId
     *
     * @param string $eventId
     * @return EventModification
     */
    public function setEventId($eventId)
    {
        $this->eventId = $eventId;

        return $this;
    }

    /**
     * Get eventId
     *
     * @return string 
     */
    public function getEventId()
    {
        return $this->eventId;
    }

    /**
     * Set eventTitre
     *
     * @param string $eventTitre
     * @return EventModification
     */
    public function setEventTitre($eventTitre)
    {
        $this->eventTitre = $eventTitre;

        return $this;
    }

    /**
     * Get eventTitre
     *
     * @return string 
     */
    public function getEventTitre()
    {
        return $this->eventTitre;
    }

    /**
     * Set professors
     *
     * @param array $professors
     * @return EventModification
     */
    public function setProfessors($professors)
    {
        $this->professors = $professors;

        return $this;
    }

    /**
     * Get professors
     *
     * @return array 
     */
    public function getProfessors()
    {
        return $this->professors;
    }

    /**
     * Set groupes
     *
     * @param array $groupes
     * @return EventModification
     */
    public function setGroupes($groupes)
    {
        $this->groupes = $groupes;

        return $this;
    }

    /**
     * Get groupes
     *
     * @return array 
     */
    public function getGroupes()
    {
        return $this->groupes;
    }
    
    /**
     * Set startDateTimeInitial
     *
     * @param \DateTime $startDateTimeInitial
     * @return EventModification
     */
    public function setStartDateTimeInitial($startDateTimeInitial)
    {
        $this->startDateTimeInitial = $startDateTimeInitial;

        return $this;
    }

    /**
     * Get startDateTimeInitial
     *
     * @return \DateTime 
     */
    public function getStartDateTimeInitial()
    {
        return $this->startDateTimeInitial;
    }

    /**
     * Set endDateTimeInitial
     *
     * @param \DateTime $endDateTimeInitial
     * @return EventModification
     */
    public function setEndDateTimeInitial($endDateTimeInitial)
    {
        $this->endDateTimeInitial = $endDateTimeInitial;

        return $this;
    }

    /**
     * Get endDateTimeInitial
     *
     * @return \DateTime 
     */
    public function getEndDateTimeInitial()
    {
        return $this->endDateTimeInitial;
    }

    /**
     * Set startDateTimeFinal
     *
     * @param \DateTime $startDateTimeFinal
     * @return EventModification
     */
    public function setStartDateTimeFinal($startDateTimeFinal)
    {
        $this->startDateTimeFinal = $startDateTimeFinal;

        return $this;
    }

    /**
     * Get startDateTimeFinal
     *
     * @return \DateTime 
     */
    public function getStartDateTimeFinal()
    {
        return $this->startDateTimeFinal;
    }

    /**
     * Set endDateTimeFinal
     *
     * @param \DateTime $endDateTimeFinal
     * @return EventModification
     */
    public function setEndDateTimeFinal($endDateTimeFinal)
    {
        $this->endDateTimeFinal = $endDateTimeFinal;

        return $this;
    }

    /**
     * Get endDateTimeFinal
     *
     * @return \DateTime 
     */
    public function getEndDateTimeFinal()
    {
        return $this->endDateTimeFinal;
    }
}
