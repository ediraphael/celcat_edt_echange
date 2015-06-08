<?php

namespace CelcatManagement\AppBundle\EventListener;

use CelcatManagement\AppBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;
use Doctrine\ORM\EntityManager;

class CalendarEventListener {

    private $schedulerManager;
    private $arrayIdTest = array(
        '265036',
        '269660',
        '274487',
        '274495',
        '265415',
    );

    public function __construct() {
        $this->schedulerManager = new \CelcatManagement\CelcatReaderBundle\Models\ScheduleManager();
    }

    public function loadEvents(CalendarEvent $calendarEvent) {
        $startDate = $calendarEvent->getStartDatetime();
        $endDate = $calendarEvent->getEndDatetime();
        $url = $calendarEvent->getRequest()->request->get('urlPath');
        $calendars = $calendarEvent->getRequest()->request->get('calendars');

        if (is_array($calendars)) {
            foreach ($calendars as $calendar) {
                $path = $url . $calendar . '.xml';
                $this->schedulerManager->parseAllSchedule($path);
            }
        } else {
            if (!preg_match('/\?/', $url)) {
                $path = $url . $calendars . '.xml';
            } else {
                $path = $url;
            }
            $this->schedulerManager->parseAllSchedule($path);
        }

        $this->feedCalendarEvent($calendarEvent);
    }
    
    public function refreshEvents(CalendarEvent $calendarEvent) {
        $this->feedCalendarEvent($calendarEvent);
    }
    
    public function feedCalendarEvent(CalendarEvent $calendarEvent) {
        $arrayWeeks = $this->schedulerManager->getArrayWeeks();
        foreach ($arrayWeeks as $indexWeek => $week) {
            foreach ($week->getArrayDays() as $indexDay => $day) {
                foreach ($day->getArrayEvents() as $indexEvents => $event) {
                    if (!$event->isDeleted()) {

                        if (in_array($event->getId(), $this->arrayIdTest)) {
                            $event->addProfessor('PILLIE RAPHAEL');
                            $event->addProfessor('POTTIER PIERRE-MARIE');
                            $event->addProfessor('DAOUDI MOHAMED');
                            $event->addFormation('2314');
                        }
                        $calendarEvent->addEvent($event);
                    }
                }
            }
        }
    }


}
