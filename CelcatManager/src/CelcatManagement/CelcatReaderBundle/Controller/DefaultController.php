<?php

namespace CelcatManagement\CelcatReaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\DomCrawler\Crawler;
use CelcatManagement\CelcatReaderBundle\Models\Event;

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
}
