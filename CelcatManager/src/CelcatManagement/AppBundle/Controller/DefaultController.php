<?php

namespace CelcatManagement\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CelcatManagement\AppBundle\Event\CalendarEvent;

class DefaultController extends Controller {

    public function indexAction(Request $request) {
        $user = $this->getUser();
        $startDatetime = new \DateTime($request->request->get('start'));        
        $endDatetime = new \DateTime($request->request->get('end'));
        /* @var $user \CelcatManagement\AppBundle\Security\User */
        
        $urlPath = $this->container->getParameter('celcat.url') . $this->container->getParameter('celcat.studentPath');
        $calendarsUser = $user->getCalendars();
        $calendars = array();
        foreach ($calendarsUser as $calendarUser) {
            $calendars[] = $calendarUser->getCalendarFile();
        }
        $request->request->add(array('urlPath' => $urlPath));
        $request->request->add(array('calendars' => $calendars));
        $this->container->get('event_dispatcher')->dispatch(CalendarEvent::CONFIGURE, new CalendarEvent($startDatetime, $endDatetime, $request))->getEvents();

        
        $url = $this->container->getParameter('celcat.url') . $this->container->getParameter('celcat.teacherPath') . $this->container->getParameter('celcat.teacherFileGet');
        $argument = '?' . $this->container->getParameter('celcat.teacherFileArgumentId') . '=' . $user->getIdentifier() .
                '&' . $this->container->getParameter('celcat.teacherFileArgumentKey') . '=' . mktime(0, 0, 0, date("m"), date("d"), date("Y"));

        $calendarFileSource = $url . $argument;
        $request->request->add(array('urlPath' => $calendarFileSource));
        $request->request->add(array('calendars' => ''));
        $this->container->get('event_dispatcher')->dispatch(CalendarEvent::CONFIGURE, new CalendarEvent($startDatetime, $endDatetime, $request))->getEvents();

        
        return $this->render('CelcatManagementAppBundle:Default:index.html.twig', array(
        ));
    }

}
