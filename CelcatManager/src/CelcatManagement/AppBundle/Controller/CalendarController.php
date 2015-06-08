<?php

namespace CelcatManagement\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CelcatManagement\AppBundle\Event\CalendarEvent;

class CalendarController extends Controller {

    public function indexAction(Request $request) {
        $groupManager = new \CelcatManagement\CelcatReaderBundle\Models\GroupManager();
        $url = $this->container->getParameter('celcat.url') . $this->container->getParameter('celcat.studentPath') . $this->container->getParameter('celcat.groupIndex');
        //$url = $this->container->getParameter('celcat.url').$this->container->getParameter('celcat.teacherPath').$this->container->getParameter('celcat.personnelIndex');

        $groupManager->loadGroups($url);

        return $this->render('CelcatManagementAppBundle:Calendar:index.html.twig', array('groupList' => $groupManager->getGroupList()));
    }
    
    
    public function userOwnThisEvent($event, $current_user, $ldapManager)
    {
        $eventProfessors = $event->getProfessors();
        $userOwnThisEvent = false;
        foreach ($eventProfessors as $eventProfessor) {
            $indexArray = str_replace(",", "", $eventProfessor);
            if (!isset($userProfessors[$indexArray]) || $userProfessors[$indexArray] != '') {
                $userProfessor = $ldapManager->getUserByFullName($eventProfessor);
                $userProfessors[$indexArray] = $userProfessor;
            }
            if ($userProfessors[$indexArray] != null && $userProfessors[$indexArray]->getUsername() == $current_user->getUsername()) {
                $userOwnThisEvent = true;
            }
        }
        return $userOwnThisEvent;
    }

    /**
     * Dispatch a CalendarEvent and return a JSON Response of any events returned.
     * 
     * @param Request $request
     * @return Response
     */
    public function loadCalendarAction(Request $request) {
        $ldapManager = $this->get('ldap_manager');
        /* @var $ldapManager \CelcatManagement\LDAPManagerBundle\LDAP\LDAPManager */
        $userProfessors = array();
        /* @var $userProfessors \CelcatManagement\AppBundle\Security\User[] */
        $startDatetime = new \DateTime($request->request->get('start'));
        $endDatetime = new \DateTime($request->request->get('end'));

        $urlPath = $this->container->getParameter('celcat.url') . $this->container->getParameter('celcat.studentPath');

        $request->request->add(array('urlPath' => $urlPath));
        $events = $this->container->get('event_dispatcher')->dispatch(CalendarEvent::CONFIGURE, new CalendarEvent($startDatetime, $endDatetime, $request))->getEvents();

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'application/json');

        $return_events = array();
        $current_user = $this->getUser();
        /* @var $current_user \CelcatManagement\AppBundle\Security\User */
        
        foreach ($events as $event) {
//            $event->setBgColor("");
            $return_events[] = $event->toArray($this->userOwnThisEvent($event, $current_user, $ldapManager));
        }

        $response->setContent(json_encode($return_events));

        return $response;
    }

    /**
     * Dispatch a CalendarEvent and return a JSON Response of any events returned.
     * 
     * @param Request $request
     * @return Response
     */
    public function refreshCalendarAction(Request $request) {
        $ldapManager = $this->get('ldap_manager');
        $startDatetime = new \DateTime($request->request->get('start'));
        $endDatetime = new \DateTime($request->request->get('end'));
        $events = $this->container->get('event_dispatcher')->dispatch(CalendarEvent::CONFIGURE_REFRESH, new CalendarEvent($startDatetime, $endDatetime, $request))->getEvents();

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'application/json');

        $return_events = array();
        $current_user = $this->getUser();
        foreach ($events as $event) {
            $return_events[] = $event->toArray($this->userOwnThisEvent($event, $current_user, $ldapManager));
        }


        $response->setContent(json_encode($return_events));

        return $response;
    }
    
    public function loadCalendarModificationsAction(Request $request) {
        $scheduleManager = new \CelcatManagement\CelcatReaderBundle\Models\ScheduleManager();
        $scheduleModifications = $scheduleManager->getScheduleModifications();
        
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($scheduleModifications));

        return $response;
    }

    public function loadEventCalendarAction(Request $request) {
        $ldapManager = $this->get('ldap_manager');
        $startDatetime = new \DateTime($request->request->get('start'));
        $endDatetime = new \DateTime($request->request->get('end'));

        $urlPath = $this->container->getParameter('celcat.url') . $this->container->getParameter('celcat.studentPath');
        $request->request->add(array('urlPath' => $urlPath));
        $events = $this->container->get('event_dispatcher')->dispatch(CalendarEvent::CONFIGURE, new CalendarEvent($startDatetime, $endDatetime, $request))->getEvents();

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'application/json');

        $return_events = array();
        $current_user = $this->getUser();
        foreach ($events as $event) {
            if ($current_user->calendarExists($event->getFormations())) {
                $return_events[$event->getId()] = $event->toArray($this->userOwnThisEvent($event, $current_user, $ldapManager));
            }
        }

        $response->setContent(json_encode($return_events));

        return $response;
    }

    public function canSwapTwoEventsAction(Request $request) {
        $schedulerManager = new \CelcatManagement\CelcatReaderBundle\Models\ScheduleManager();
        $obj_event_source = $request->request->get('event_source');
        $obj_events_destination = $request->request->get('events_destination');

        $event_source = $schedulerManager->getWeekByTag($obj_event_source['week'])->getDayById($obj_event_source['day'])->getEventById($obj_event_source['id']);
        $result = array();
        $current_user = $this->getUser();
        foreach ($obj_events_destination as $obj_event_destination) {
            $event_destination = $schedulerManager->getWeekByTag($obj_event_destination['week'])->getDayById($obj_event_destination['day'])->getEventById($obj_event_destination['id']);
            $result[] = array("id" => $event_destination->getId(), "result" => $schedulerManager->canSwapEvent($event_source, $event_destination, $current_user));
        }

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($result));
        return $response;
    }

    public function swapTwoEventsAction(Request $request) {
        $schedulerManager = new \CelcatManagement\CelcatReaderBundle\Models\ScheduleManager();
        $obj_event_source = $request->request->get('event_source');
        $obj_event_destination = $request->request->get('event_destination');
        $event_source = $schedulerManager->getWeekByTag($obj_event_source['week'])->getDayById($obj_event_source['day'])->getEventById($obj_event_source['id']);
        $event_destination = $schedulerManager->getWeekByTag($obj_event_destination['week'])->getDayById($obj_event_destination['day'])->getEventById($obj_event_destination['id']);

        $result = $schedulerManager->swapEvent($event_source, $event_destination);

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($result));
        return $response;
    }

}
