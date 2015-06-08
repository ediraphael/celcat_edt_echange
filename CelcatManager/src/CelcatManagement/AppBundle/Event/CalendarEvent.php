<?php

namespace CelcatManagement\AppBundle\Event;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpFoundation\Request;

use ADesigns\CalendarBundle\Entity\EventEntity;

class CalendarEvent extends Event
{
    const CONFIGURE = 'calendar.load_events';
    const CONFIGURE_REFRESH = 'calendar.refresh_events';
    
    private $startDatetime;
    
    private $endDatetime;
    
    private $request;

    private $events;
    
    /**
     *
     * @var \CelcatManagement\AppBundle\Security\User 
     */
    private $user;
    
    /**
     * Constructor method requires a start and end time for event listeners to use.
     * 
     * @param \DateTime $start Begin datetime to use
     * @param \DateTime $end End datetime to use
     */
    public function __construct(\DateTime $start, \DateTime $end, Request $request = null, $user)
    {
        $this->startDatetime = $start;
        $this->endDatetime = $end;
        $this->request = $request;
        $this->events = new \Doctrine\Common\Collections\ArrayCollection();
        $this->user = $user;
    }

    public function getEvents()
    {
        return $this->events;
    }
    
    /**
     * If the event isn't already in the list, add it
     * 
     * @param \CelcatManagement\CelcatReaderBundle\Models\Event $event
     * @return CalendarEvent $this
     */
    public function addEvent(\CelcatManagement\CelcatReaderBundle\Models\Event $event)
    {
        if (!$this->events->contains($event)) {
            $this->events[] = $event;
        }
        
        return $this;
    }
    
    /**
     * Get start datetime 
     * 
     * @return \DateTime
     */
    public function getStartDatetime()
    {
        return $this->startDatetime;
    }

    /**
     * Get end datetime 
     * 
     * @return \DateTime
     */
    public function getEndDatetime()
    {
        return $this->endDatetime;
    }

    /**
     * Get request
     * 
     * @return Request
     */
    public function getRequest()
    {
        return $this->request;
    }
    
    public function getUser() {
        return $this->user;
    }

    public function setUser($user) {
        $this->user = $user;
        return $this;
    }


}
