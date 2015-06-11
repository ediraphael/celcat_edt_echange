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
            $return_events[] = $event->toArray();
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
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        $ldapManager = $this->get('ldap_manager');
        $startDatetime = new \DateTime($request->request->get('start'));
        $endDatetime = new \DateTime($request->request->get('end'));
        $eventSourceJs = $request->request->get('event_source');
        $eventDestinationJs = $request->request->get('event_destination');
        $removedScheduleModification = $request->request->get('removed_schedule_modification');
        $dropedEventModification = $request->request->get('droped_event_modification');
        $resizedEventModification = $request->request->get('resized_event_modification');

        $scheduleManager = new \CelcatManagement\CelcatReaderBundle\Models\ScheduleManager();

        $entities = $em->getRepository('CelcatManagementAppBundle:ScheduleModification')->findAll();
        /* @var $entities \CelcatManagement\AppBundle\Entity\ScheduleModification[] */
        foreach ($entities as $entity) {
            $idFirstEvent = $entity->getFirstEvent()->getEventId();
            $firstEvent = $scheduleManager->getEventById($idFirstEvent);
            if ($firstEvent != null && !$firstEvent->hasReplacementEvent()) {
                $replacementEvent = clone $firstEvent;
                $replacementEvent->setStartDatetime($entity->getFirstEvent()->getStartDateTimeFinal());
                $replacementEvent->setEndDatetime($entity->getFirstEvent()->getEndDateTimeFinal());
                $firstEvent->replaceBy($replacementEvent);
            }
            if ($entity->getSecondEvent() != null) {
                $idSecondEvent = $entity->getSecondEvent()->getEventId();
                $secondEvent = $scheduleManager->getEventById($idSecondEvent);
                if ($secondEvent != null && !$secondEvent->hasReplacementEvent()) {
                    $replacementEvent = clone $secondEvent;
                    $replacementEvent->setStartDatetime($entity->getSecondEvent()->getStartDateTimeFinal());
                    $replacementEvent->setEndDatetime($entity->getSecondEvent()->getEndDateTimeFinal());
                    $secondEvent->replaceBy($replacementEvent);
                }
            }
        }

        if ($removedScheduleModification != null && $removedScheduleModification != '') {
            $scheduleManager->removeScheduleModificationById($removedScheduleModification);
        }

        if ($dropedEventModification != null && $dropedEventModification != '') {
            $eventModifiedJs = json_decode($dropedEventModification);
            $eventSource = $scheduleManager->getEventById($eventModifiedJs->id);
            $eventSourceIsSwaped = $eventSource->isSwaped();
            $eventSource->deleteReplacementEvent();
            $replacementEvent = clone $eventSource;
            if ($eventSourceIsSwaped) {
                $replacementEvent->swaped();
            }
            $startDatetime = new \DateTime($eventModifiedJs->start);
            $replacementEvent->setStartDatetime($startDatetime);
            $replacementEvent->setEndDatetime(new \DateTime($eventModifiedJs->end));

            $eventSource->replaceBy($replacementEvent);
            $scheduleModification = new \CelcatManagement\CelcatReaderBundle\Models\ScheduleModification();
            $scheduleModification->setDropModification($eventSource);
            $scheduleManager->addScheduleModification($scheduleModification);
        }

        if ($resizedEventModification != null && $resizedEventModification != '') {
            $eventModifiedJs = json_decode($resizedEventModification);

            $eventSource = $scheduleManager->getEventById($eventModifiedJs->id);
            $eventSourceIsSwaped = $eventSource->isSwaped();
            $eventSource->deleteReplacementEvent();
            $replacementEvent = clone $eventSource;
            if ($eventSourceIsSwaped) {
                $replacementEvent->swaped();
            }
            $replacementEvent->setStartDatetime(new \DateTime($eventModifiedJs->start));
            $replacementEvent->setEndDatetime(new \DateTime($eventModifiedJs->end));
            $eventSource->replaceBy($replacementEvent);
            $scheduleModification = new \CelcatManagement\CelcatReaderBundle\Models\ScheduleModification();
            $scheduleModification->setResizeModification($eventSource);
            $scheduleManager->addScheduleModification($scheduleModification);
        }

        //On réinitialise les evenements
        foreach ($scheduleManager->getEvents() as $indexEvents => $firstEvent) {
            $firstEvent->unEventSource();
            $firstEvent->unswapable();
            $firstEvent->delete();
            if ($firstEvent->containsFormations($user->getIdentifier())) {
                $firstEvent->clickable();
            } else {
                $firstEvent->unclickable();
            }
        }

        // On verifie si on vient de faire un click sur un premier evenement
        if ($eventSourceJs != null && $eventSourceJs != '' && ($eventDestinationJs == null || $eventDestinationJs == '')) {
            //Dans ce cas on recherche tout ce qui sont swapable
            $eventSource = $scheduleManager->getEventById($eventSourceJs['id']);

            foreach ($scheduleManager->getEvents() as $indexEvents => $firstEvent) {
                if ($firstEvent->getId() != $eventSource->getId()) {
                    if ($firstEvent->containsFormations($eventSource->getFormations(), true)) {
                        if ($scheduleManager->canSwapEvent($eventSource, $firstEvent, $user, $this->container)) {
                            $firstEvent->swapable();
                        }
                        $firstEvent->undelete();
                    }
                } else {
                    $firstEvent->eventSource();
                    $firstEvent->undelete();
                }
            }
        }

        if ($eventSourceJs != null && $eventSourceJs != '' && $eventDestinationJs != null && $eventDestinationJs != '') {
            $eventSource = $scheduleManager->getEventById($eventSourceJs['id']);
            $eventDestination = $scheduleManager->getEventById($eventDestinationJs['id']);
            $scheduleManager->swapEvent($eventSource, $eventDestination);
        }


        $scheduleManager->save();
        $events = $this->container->get('event_dispatcher')->dispatch(CalendarEvent::CONFIGURE_REFRESH, new CalendarEvent($startDatetime, $endDatetime, $request, $this->getUser()))->getEvents();

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'application/json');

        $return_events = array();
        foreach ($events as $firstEvent) {
            $return_events[] = $firstEvent->toArray();
        }

        $response->setContent(json_encode($return_events));

        return $response;
    }

    public function loadCalendarModificationsAction(Request $request) {
        $scheduleManager = new \CelcatManagement\CelcatReaderBundle\Models\ScheduleManager();
        $scheduleModifications = $scheduleManager->getScheduleModifications();

        $return_events = array();
        foreach ($scheduleModifications as $cle => $scheduleModification) {
            if ($scheduleModification->getId() == '') {
                $return_events[] = $scheduleModification->toArray();
            }
        }

        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'application/json');
        $response->setContent(json_encode($return_events));

        return $response;
    }

}
