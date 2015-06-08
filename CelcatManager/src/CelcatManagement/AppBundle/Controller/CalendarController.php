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

    public function userOwnThisEvent($event, $current_user, $ldapManager) {
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
        $events = $this->container->get('event_dispatcher')->dispatch(CalendarEvent::CONFIGURE, new CalendarEvent($startDatetime, $endDatetime, $request, $this->getUser()))->getEvents();

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'application/json');

        $return_events = array();
        $current_user = $this->getUser();
        /* @var $current_user \CelcatManagement\AppBundle\Security\User */
        /* @var $event \CelcatManagement\CelcatReaderBundle\Models\Event */
        foreach ($events as $event) {
            if ($event->hasReplacementEvent()) {
                $event->setBgColor("purple");
                $ownEvent = false;
            } else {
                $event->setBgColor("");
                $ownEvent = $this->userOwnThisEvent($event, $current_user, $ldapManager);
            }
            $return_events[] = $event->toArray($ownEvent);
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
        $user = $this->getUser();
        $ldapManager = $this->get('ldap_manager');
        $startDatetime = new \DateTime($request->request->get('start'));
        $endDatetime = new \DateTime($request->request->get('end'));
        $eventSourceJs = $request->request->get('event_source');
        $eventDestinationJs = $request->request->get('event_destination');


        $scheduleManager = new \CelcatManagement\CelcatReaderBundle\Models\ScheduleManager();
        //On réinitialise les evenements
        $arrayWeeks = $scheduleManager->getArrayWeeks();
        foreach ($arrayWeeks as $indexWeek => $week) {
            foreach ($week->getArrayDays() as $indexDay => $day) {
                foreach ($day->getArrayEvents() as $indexEvents => $event) {
                    $event->unEventSource();
                    $event->unswapable();
                    $event->delete();
                }
            }
        }
       

        // On verifie si on vient de faire un click sur un premier evenement
        if ($eventSourceJs != null && $eventSourceJs != '' && ($eventDestinationJs == null || $eventDestinationJs == '')) {
            //Dans ce cas on recherche tout ce qui sont swapable
            $eventSource = $scheduleManager->getWeekByTag($eventSourceJs['week'])->getDayById($eventSourceJs['day'])->getEventById($eventSourceJs['id']);
            foreach ($arrayWeeks as $indexWeek => $week) {
                foreach ($week->getArrayDays() as $indexDay => $day) {
                    foreach ($day->getArrayEvents() as $indexEvents => $event) {
                        if ($event->getId() != $eventSource->getId()) {
                            if ($scheduleManager->canSwapEvent($eventSource, $event, $user)) {
                                $event->swapable();
                            } 
                            else {
                                $formations = $event->getFormations()->toArray();
                                foreach ($formations as $formation) {
                                    if(in_array($formation, $eventSource->getFormations()->toArray())) {
                                        $event->undelete();
                                    }
                                }
                            }
                        } else {
                            $event->eventSource();
                        }
                    }
                }
            }
        }
        
        if ($eventSourceJs != null && $eventSourceJs != '' && $eventDestinationJs != null && $eventDestinationJs != '') {
            $eventSource = $scheduleManager->getWeekByTag($eventSourceJs['week'])->getDayById($eventSourceJs['day'])->getEventById($eventSourceJs['id']);
            $eventDestination = $scheduleManager->getWeekByTag($eventDestinationJs['week'])->getDayById($eventDestinationJs['day'])->getEventById($eventDestinationJs['id']);
            $scheduleManager->swapEvent($eventSource, $eventDestination);
        }


         $scheduleManager->save();
        $events = $this->container->get('event_dispatcher')->dispatch(CalendarEvent::CONFIGURE_REFRESH, new CalendarEvent($startDatetime, $endDatetime, $request, $this->getUser()))->getEvents();

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'application/json');

        $return_events = array();
        foreach ($events as $event) {
            $return_events[] = $event->toArray($this->userOwnThisEvent($event, $user, $ldapManager));
        }


        $response->setContent(json_encode($return_events));

        return $response;
    }

    public function loadCalendarModificationsAction(Request $request) {
        $scheduleManager = new \CelcatManagement\CelcatReaderBundle\Models\ScheduleManager();
        $scheduleModifications = $scheduleManager->getScheduleModifications();

        $return_events = array();
        foreach ($scheduleModifications as $cle => $scheduleModification) {
            $return_events[] = $scheduleModification->toArray();
        }

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($return_events));

        return $response;
    }
}
