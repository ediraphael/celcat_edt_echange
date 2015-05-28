<?php

namespace CelcatManagement\AppBundle\EventListener;

use CelcatManagement\AppBundle\Event\CalendarEvent;
use ADesigns\CalendarBundle\Entity\EventEntity;
use Doctrine\ORM\EntityManager;

class CalendarEventListener
{
    private $entityManager;

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
       
        //$calendarEvent->getRequest()->request->get('calendar')

        // $companyEvents and $companyEvent in this example
        // represent entities from your database, NOT instances of EventEntity
        // within this bundle.
        //
        // Create EventEntity instances and populate it's properties with data
        // from your own entities/database values.

//        foreach($companyEvents as $companyEvent) {

            // create an event with a start/end time, or an all day event
//            if ($companyEvent->getAllDayEvent() === false) {
//                $eventEntity = new EventEntity($companyEvent->getTitle(), $companyEvent->getStartDatetime(), $companyEvent->getEndDatetime());
//            } else {
//                $eventEntity = new EventEntity($companyEvent->getTitle(), $companyEvent->getStartDatetime(), null, true);
//            }
//            $eventEntity = new EventEntity("toto", new \DateTime(), null, true);
//            //optional calendar event settings
//            $eventEntity->setAllDay(true); // default is false, set to true if this is an all day event
//            $eventEntity->setBgColor('#FF0000'); //set the background color of the event's label
//            $eventEntity->setFgColor('#FFFFFF'); //set the foreground color of the event's label
//            $eventEntity->setUrl('http://www.google.com'); // url to send user to when event label is clicked
//            $eventEntity->setCssClass('my-custom-class'); // a custom class you may want to apply to event labels

            //finally, add the event to the CalendarEvent for displaying on the calendar
            //$calendarEvent->addEvent($eventEntity);
//        }
    }
}