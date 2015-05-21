<?php

namespace CelcatManagement\CelcatReaderBundle\Models;
use Symfony\Component\DomCrawler\Crawler;

class ScheduleManager
{
    
    private $tab_weeks;
            
    
    function __construct() 
    {
        $this->tab_weeks = array();
    }
    
    public function getTab_weeks() {
        return $this->tab_weeks;
    }

    public function setTab_weeks($tab_weeks) {
        $this->tab_weeks = $tab_weeks;
    }
    
    public function addWeek($week)
    {
        $this->tab_weeks[] = $week;
    }
    
    public function weekExists($week_id)
    {
        foreach ($this->tab_weeks as $week)
        {
            if($week->getId() == $week_id)
                return true;
        }
        return false;
    }
    
    public function getWeekById($week_id)
    {
        foreach ($this->tab_weeks as $week)
        {
            if($week->getId() == $week_id)
                return $week;
        }
        return null;
    }
    
    public function getWeekByTag($week_tag)
    {
        foreach ($this->tab_weeks as $week)
        {
            if($week->getTag() == $week_tag)
                return $week;
        }
        return null;
    }
    
    public function parseWeeks($file_contents)
    {
        try 
        {
            $crawler = new Crawler();
            $crawler->addXmlContent($file_contents);
            $return_value = $crawler->filterXPath("//span");
            foreach ($return_value as $node)
            {
                $crawler = new Crawler();
                $crawler->add($node);
                if(!$this->weekExists($crawler->filterXPath("//title")->text()))
                {
                    $week = new Week();
                    $week->setDate($crawler->attr("date"));
                    $week->setId($crawler->filterXPath("//title")->text());
                    $week->setDescription($crawler->filterXPath("//description")->text());
                    $week->setTag($crawler->filterXPath("//alleventweeks")->text());
                    $this->addWeek($week);
                }

            }
        } catch(Exception $e)
        {
            print_r($e);
        }
    }
    
    
    public function parseEvents($file_contents, $formation_id)
    {
        try 
        {
            $crawler = new Crawler();
            $crawler->addXmlContent($file_contents);
            $return_value = $crawler->filterXPath("//event");
            foreach ($return_value as $node)
            {
                $crawler = new Crawler();
                $crawler->add($node);
                $event = new Event();
                $event->setFormation($formation_id);
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

                $this->getWeekByTag($event->getWeek())->getDayById($event->getDay())->addEvent($event);
            }
        } catch(Exception $e)
        {
            print_r($e);
        }
    }
    
//    passage d'un fichier XML
    public function parseAllSchedule($file_name)
    {
        $formation_id = explode(".", $file_name)[0];
        $file_contents = file_get_contents("http://celcat.univ-angers.fr/web/publi/etu/".$file_name);
        $this->parseWeeks($file_contents);
        $this->parseEvents($file_contents, $formation_id);
    }
    
    
//    recupere la liste des possibilités
    public function getFreeEventsList($event_source, $event_destination)
    {
        $tab_free_events = $this->getWeekByTag($event_destination->getWeek())
                ->getDayById($event_destination->getDay())->getFreeEventsList($event_source, $event_destination);
        
    }
    
//    tester si un créneau peut etre changer vers un autre
    public function canSwapEvent($event_source, $event_destination)
    {
        if($this->getWeekByTag($event_destination->getWeek())->getDayById($event_destination->getDay())
                ->canAddEvent($event_destination->getStart_time(), $event_destination
                        ->getEnd_time(), $event_destination->getFormation()))
        {
//            on peut ajouter un créneau à ce jour ci
            
        }    
        else
        {
//            on ne peut pas ajouter un créneau (donc il faut proposer une liste de propositions)
            
        }
            
    }


}

