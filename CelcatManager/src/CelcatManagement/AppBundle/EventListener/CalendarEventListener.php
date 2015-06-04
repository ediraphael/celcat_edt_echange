<?php

namespace CelcatManagement\AppBundle\EventListener;

use CelcatManagement\AppBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;
use Doctrine\ORM\EntityManager;

class CalendarEventListener {

    private $schedulerManager;

    public function __construct() {
        $this->schedulerManager = new \CelcatManagement\CelcatReaderBundle\Models\ScheduleManager();
    }

    public function loadEvents(CalendarEvent $calendarEvent) {
        $startDate = $calendarEvent->getStartDatetime();
        $endDate = $calendarEvent->getEndDatetime();
        $url = $calendarEvent->getRequest()->request->get('urlPath');

        $this->schedulerManager->parseAllSchedule($url);
        $arrayWeeks = $this->schedulerManager->getArrayWeeks();

        foreach ($arrayWeeks as $indexWeek => $week) {
            foreach ($week->getArrayDays() as $indexDay => $day) {
                foreach ($day->getArrayEvents() as $indexEvents => $event) {
                    if (!$event->isDeleted()) {
                        $calendarEvent->addEvent($event);
                    }
                }
            }
        }
    }

    public function refreshEvents(CalendarEvent $calendarEvent) {
        $arrayWeeks = $this->schedulerManager->getArrayWeeks();
        foreach ($arrayWeeks as $indexWeek => $week) {
            foreach ($week->getArrayDays() as $indexDay => $day) {
                foreach ($day->getArrayEvents() as $indexEvents => $event) {
                    if (!$event->isDeleted()) {
                        $calendarEvent->addEvent($event);
                    }
                }
            }
        }
    }

}
