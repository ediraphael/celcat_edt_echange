<?php

namespace CelcatManagement\CelcatReaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use CelcatManagement\CelcatReaderBundle\Models\Event;
use CelcatManagement\CelcatReaderBundle\Models\Week;
use CelcatManagement\CelcatReaderBundle\Models\Day;
use CelcatManagement\CelcatReaderBundle\Models\ScheduleManager;

class DefaultController extends Controller
{
    public function indexAction($name = "")
    {
        $groupManager = new \CelcatManagement\CelcatReaderBundle\Models\GroupManager();
        $url = $this->container->getParameter('celcat.url').$this->container->getParameter('celcat.studentPath').$this->container->getParameter('celcat.groupIndex');
        $groupManager->loadGroups($url);
        
        return $this->render('CelcatManagementCelcatReaderBundle:Default:index.html.twig', array('name' => $name, 'groupList' => $groupManager->getGroupList()));
    } 
    
    public function parseAllScheduleAction($file_name = "")
    {       
        $scheduleManager = new ScheduleManager();
        
        $url = $this->container->getParameter('celcat.url').$this->container->getParameter('celcat.studentPath').$file_name;
        $scheduleManager->parseAllSchedule($url);
        //var_dump($scheduleManager->getTab_weeks());
//        $event_source = $scheduleManager->getWeekById(22)->getDayById(3)->getEventByIdAndByFormation("238656", "g200872");
//        $event_destination = $scheduleManager->getWeekById(22)->getDayById(3)->getEventByIdAndByFormation("238656", "g200872");
        
        $event_source = $scheduleManager->getWeekById(23)->getDayById(3)->getEventByIdAndByFormation("269741", "g200872");
        $event_destination = $scheduleManager->getWeekById(23)->getDayById(3)->getEventByIdAndByFormation("269741", "g200872");
        
        $tab_test = $scheduleManager->getFreeEventsList($event_source, $event_destination);
        
        return $this->render('CelcatManagementCelcatReaderBundle:Default:parse.html.twig', array('test' => $tab_test, 'scheduleManager' => $scheduleManager));
    }
    
    
}
