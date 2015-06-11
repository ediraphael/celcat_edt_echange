<?php

namespace CelcatManagement\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ScheduleModification
 */
class ScheduleModification
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $user;

    /**
     * @var boolean
     */
    private $canceled;

    /**
     * @var boolean
     */
    private $validated;

    /**
     * @var \CelcatManagement\AppBundle\Entity\EventModification
     */
    private $firstEvent;

    /**
     * @var \CelcatManagement\AppBundle\Entity\EventModification
     */
    private $secondEvent;


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
     * Set user
     *
     * @param string $user
     * @return ScheduleModification
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set canceled
     *
     * @param boolean $canceled
     * @return ScheduleModification
     */
    public function setCanceled($canceled)
    {
        $this->canceled = $canceled;

        return $this;
    }

    /**
     * Get canceled
     *
     * @return boolean 
     */
    public function getCanceled()
    {
        return $this->canceled;
    }

    /**
     * Set validated
     *
     * @param boolean $validated
     * @return ScheduleModification
     */
    public function setValidated($validated)
    {
        $this->validated = $validated;

        return $this;
    }

    /**
     * Get validated
     *
     * @return boolean 
     */
    public function getValidated()
    {
        return $this->validated;
    }

    /**
     * Set firstEvent
     *
     * @param \CelcatManagement\AppBundle\Entity\EventModification $firstEvent
     * @return ScheduleModification
     */
    public function setFirstEvent(\CelcatManagement\AppBundle\Entity\EventModification $firstEvent = null)
    {
        $this->firstEvent = $firstEvent;

        return $this;
    }

    /**
     * Get firstEvent
     *
     * @return \CelcatManagement\AppBundle\Entity\EventModification 
     */
    public function getFirstEvent()
    {
        return $this->firstEvent;
    }

    /**
     * Set secondEvent
     *
     * @param \CelcatManagement\AppBundle\Entity\EventModification $secondEvent
     * @return ScheduleModification
     */
    public function setSecondEvent(\CelcatManagement\AppBundle\Entity\EventModification $secondEvent = null)
    {
        $this->secondEvent = $secondEvent;

        return $this;
    }

    /**
     * Get secondEvent
     *
     * @return \CelcatManagement\AppBundle\Entity\EventModification 
     */
    public function getSecondEvent()
    {
        return $this->secondEvent;
    }
}
