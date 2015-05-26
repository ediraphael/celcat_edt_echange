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
        return $this->render('CelcatManagementCelcatReaderBundle:Default:index.html.twig', array('name' => $name));
    }
    
    public function parseAction($file_name = "")
    {
        $tab_events = array();
        $file_contents = file_get_contents("http://celcat.univ-angers.fr/web/publi/etu/".$file_name);
        $crawler = new Crawler();
        $crawler->addXmlContent($file_contents);
        $return_value = $crawler->filterXPath("//event");
        foreach ($return_value as $node)
        {
            $crawler = new Crawler();
            $crawler->add($node);
            $event = new Event();
            $event->setId($crawler->attr("id"));
            $event->setColour($crawler->attr("colour"));
            $event->setWeek($crawler->filterXPath("//rawweeks")->text());
            if($crawler->filterXPath("//room/item")->count() > 0)
                $event->setRoom($crawler->filterXPath("//room/item")->text());
            $event->setCategory($crawler->filterXPath("//category")->text());
            $event->setDay($crawler->filterXPath("//day")->text());
            $event->setStart_time($crawler->filterXPath("//starttime")->text());
            $event->setEnd_time($crawler->filterXPath("//endtime")->text());
            if($crawler->filterXPath("//group/item")->count() > 0)
                $event->setGroup($crawler->filterXPath("//group/item")->text());
            if($crawler->filterXPath("//module/item")->count() > 0)
                $event->setModule($crawler->filterXPath("//module/item")->text());
            if($crawler->filterXPath("//notes")->count() > 0)
                $event->setNote($crawler->filterXPath("//notes")->text());
            if($crawler->filterXPath("//staff/item")->count() > 0)
                $event->setProfessor($crawler->filterXPath("//staff/item")->text());
            if($crawler->filterXPath("//prettytimes")->count() > 0)
                $event->setTime($crawler->filterXPath("//prettytimes")->text());
            
            $tab_events[] = $event;
        }   
        
        return $this->render('CelcatManagementCelcatReaderBundle:Default:parse.html.twig', array( 'tab_events' => $tab_events));
    }
    
    public function getEventByIdAction($file_name = "", $event_id = "")
    {
        $file_contents = file_get_contents("http://celcat.univ-angers.fr/web/publi/etu/".$file_name);
        $crawler = new Crawler();
        $crawler->addXmlContent($file_contents);
        $return_value = $crawler->filterXPath("//event[@id='".$event_id."']");
        $event = new Event();
        $event->setId($return_value->attr("id"));
        $event->setColour($return_value->attr("colour"));
        $event->setWeek($return_value->filterXPath("//rawweeks")->text());
        if($return_value->filterXPath("//room/item")->count() > 0)
            $event->setRoom($return_value->filterXPath("//room/item")->text());
        $event->setCategory($return_value->filterXPath("//category")->text());
        $event->setDay($return_value->filterXPath("//day")->text());
        $event->setStart_time($return_value->filterXPath("//starttime")->text());
        $event->setEnd_time($return_value->filterXPath("//endtime")->text());
        if($return_value->filterXPath("//group/item")->count() > 0)
            $event->setGroup($return_value->filterXPath("//group/item")->text());
        if($return_value->filterXPath("//module/item")->count() > 0)
            $event->setModule($return_value->filterXPath("//module/item")->text());
        if($return_value->filterXPath("//notes")->count() > 0)
            $event->setNote($return_value->filterXPath("//notes")->text());
        if($return_value->filterXPath("//staff/item")->count() > 0)
            $event->setProfessor($return_value->filterXPath("//staff/item")->text());
        if($return_value->filterXPath("//prettytimes")->count() > 0)
            $event->setTime($return_value->filterXPath("//prettytimes")->text());

        return $this->render('CelcatManagementCelcatReaderBundle:Default:parse.html.twig', array( 'event' => $event));
    }
    
    public function getEventsByWeekAction($file_name = "", $week_id = "")
    {
        $tab_events = array();
        $file_contents = file_get_contents("http://celcat.univ-angers.fr/web/publi/etu/".$file_name);
        $crawler = new Crawler();
        $crawler->addXmlContent($file_contents);
        $week_tag = $crawler->filterXPath("//span[title='".$week_id."']/alleventweeks")->text();
        $return_value = $crawler->filterXPath("//event[rawweeks='".$week_tag."']");
        foreach ($return_value as $node)
        {
            $crawler = new Crawler();
            $crawler->add($node);
            $event = new Event();
            $event->setId($crawler->attr("id"));
            $event->setColour($crawler->attr("colour"));
            $event->setWeek($crawler->filterXPath("//rawweeks")->text());
            if($crawler->filterXPath("//room/item")->count() > 0)
                $event->setRoom($crawler->filterXPath("//room/item")->text());
            $event->setCategory($crawler->filterXPath("//category")->text());
            $event->setDay($crawler->filterXPath("//day")->text());
            $event->setStart_time($crawler->filterXPath("//starttime")->text());
            $event->setEnd_time($crawler->filterXPath("//endtime")->text());
            if($crawler->filterXPath("//group/item")->count() > 0)
                $event->setGroup($crawler->filterXPath("//group/item")->text());
            if($crawler->filterXPath("//module/item")->count() > 0)
                $event->setModule($crawler->filterXPath("//module/item")->text());
            if($crawler->filterXPath("//notes")->count() > 0)
                $event->setNote($crawler->filterXPath("//notes")->text());
            if($crawler->filterXPath("//staff/item")->count() > 0)
                $event->setProfessor($crawler->filterXPath("//staff/item")->text());
            if($crawler->filterXPath("//prettytimes")->count() > 0)
                $event->setTime($crawler->filterXPath("//prettytimes")->text());
            
            $tab_events[] = $event;
        }   
        
        return $this->render('CelcatManagementCelcatReaderBundle:Default:parse.html.twig', array('tab_events' => $tab_events));
    }
    
    
    public function parseAllScheduleAction($file_name = "")
    {       
        $scheduleManager = new ScheduleManager();
        $scheduleManager->parseAllSchedule($file_name);
        //var_dump($scheduleManager->getTab_weeks());
//        $event_source = $scheduleManager->getWeekById(22)->getDayById(3)->getEventByIdAndByFormation("238656", "g200872");
//        $event_destination = $scheduleManager->getWeekById(22)->getDayById(3)->getEventByIdAndByFormation("238656", "g200872");
        
        $event_source = $scheduleManager->getWeekById(23)->getDayById(3)->getEventByIdAndByFormation("269741", "g200872");
        $event_destination = $scheduleManager->getWeekById(23)->getDayById(3)->getEventByIdAndByFormation("269741", "g200872");
        
        $tab_test = $scheduleManager->getFreeEventsList($event_source, $event_destination);
        
        return $this->render('CelcatManagementCelcatReaderBundle:Default:parse.html.twig', array('test' => $tab_test, 'scheduleManager' => $scheduleManager));
    }
    
    
}
