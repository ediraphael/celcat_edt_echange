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
    private $dateInitial;

    /**
     * @var \DateTime
     */
    private $dateFinal;
    
    public function __construct() {
        $this->groupes = array();
        $this->professors = array();
        
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
     * Set dateInitial
     *
     * @param \DateTime $dateInitial
     * @return EventModification
     */
    public function setDateInitial($dateInitial)
    {
        $this->dateInitial = $dateInitial;

        return $this;
    }

    /**
     * Get dateInitial
     *
     * @return \DateTime 
     */
    public function getDateInitial()
    {
        return $this->dateInitial;
    }

    /**
     * Set dateFinal
     *
     * @param \DateTime $dateFinal
     * @return EventModification
     */
    public function setDateFinal($dateFinal)
    {
        $this->dateFinal = $dateFinal;

        return $this;
    }

    /**
     * Get dateFinal
     *
     * @return \DateTime 
     */
    public function getDateFinal()
    {
        return $this->dateFinal;
    }
}
