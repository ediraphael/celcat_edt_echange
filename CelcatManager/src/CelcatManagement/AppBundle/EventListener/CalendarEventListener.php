<?php

namespace CelcatManagement\AppBundle\EventListener;

use CelcatManagement\AppBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;
use Doctrine\ORM\EntityManager;

class CalendarEventListener
{
    public function __construct()
    {
    
    }

    public function loadEvents(CalendarEvent $calendarEvent)
    {
        $startDate = $calendarEvent->getStartDatetime();
        $endDate = $calendarEvent->getEndDatetime();
        $url = $calendarEvent->getRequest()->request->get('urlPath');
        $schedulerManager = new \CelcatManagement\CelcatReaderBundle\Models\ScheduleManager();
        
        
        $schedulerManager->parseAllSchedule($url);
        
        $arrayWeeks = $schedulerManager->getArrayWeeks();
        
        
        foreach ($arrayWeeks as $indexWeek => $week) {
            foreach ($week->getArrayDays() as $indexDay => $day) {
                foreach ($day->getArrayEvents() as $indexEvents => $events) {
                    foreach ($events as $groupeName => $event) {
                        $calendarEvent->addEvent($event);
                    }
                }
            }
        }
    }
}