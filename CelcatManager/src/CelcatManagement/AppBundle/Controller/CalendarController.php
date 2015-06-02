<?php

namespace CelcatManagement\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use CelcatManagement\AppBundle\Event\CalendarEvent;

class CalendarController extends Controller
{
    public function indexAction(Request $request) {
        $groupManager = new \CelcatManagement\CelcatReaderBundle\Models\GroupManager();
        $url = $this->container->getParameter('celcat.url').$this->container->getParameter('celcat.studentPath').$this->container->getParameter('celcat.groupIndex');
        $groupManager->loadGroups($url);
        
        return $this->render('CelcatManagementAppBundle:Calendar:index.html.twig', array('groupList' => $groupManager->getGroupList()));
    }
    
    /**
     * Dispatch a CalendarEvent and return a JSON Response of any events returned.
     * 
     * @param Request $request
     * @return Response
     */
    public function loadCalendarAction(Request $request)
    {
        $startDatetime = new \DateTime($request->request->get('start'));        
        $endDatetime = new \DateTime($request->request->get('end'));
        $urlPath = $this->container->getParameter('celcat.url').$this->container->getParameter('celcat.studentPath').$request->request->get('calendar');
        $request->request->add(array('urlPath' => $urlPath));
        $events = $this->container->get('event_dispatcher')->dispatch(CalendarEvent::CONFIGURE, new CalendarEvent($startDatetime, $endDatetime, $request))->getEvents();
        
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'application/json');
        
        $return_events = array();
        
        foreach($events as $event) {
            $return_events[] = $event->toArray();    
        }
        
        
        $response->setContent(json_encode($return_events));
        
        return $response;
    }
    
    public function loadEventCalendarAction(Request $request)
    {
        $startDatetime = new \DateTime($request->request->get('start'));        
        $endDatetime = new \DateTime($request->request->get('end'));
        $urlPath = $this->container->getParameter('celcat.url').$this->container->getParameter('celcat.studentPath').$request->request->get('calendar');
        $request->request->add(array('urlPath' => $urlPath));
        $events = $this->container->get('event_dispatcher')->dispatch(CalendarEvent::CONFIGURE, new CalendarEvent($startDatetime, $endDatetime, $request))->getEvents();
        
        $response = new \Symfony\Component\HttpFoundation\Response();
        $response->headers->set('Content-Type', 'application/json');
        
        $return_events = array();
        $current_user = $this->getUser();
        foreach($events as $event) 
        {
            if($current_user->calendarExists($event->getFormation()))
            {
                $return_events[$event->getId()] = $event->toArray();    
            }
        }
        
        
        $response->setContent(json_encode($return_events));
        
        return $response;
    }
    
    
}
