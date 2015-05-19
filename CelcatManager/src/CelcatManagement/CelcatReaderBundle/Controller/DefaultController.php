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
            $event = new Event();
            $event->setId($node->attributes);
            $event->setColour($node->attributes);
//            foreach($node->children() as $child_node)
            {
                
            }
            
            $tab_events[] = $event;
        }   
        
        return $this->render('CelcatManagementCelcatReaderBundle:Default:parse.html.twig', array('return_value' => $return_value));
    }
}
