<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

use Symfony\Component\DomCrawler\Crawler;

class ScheduleManager {
    /**
     *
     * @var Week[] 
     */
    private $ArrayWeeks;

    function __construct() {
        $this->ArrayWeeks = array();
    }

    public function getArrayWeeks() {
        return $this->ArrayWeeks;
    }

    public function setArrayWeeks($arrayWeeks) {
        $this->ArrayWeeks = $arrayWeeks;
    }

    public function addWeek($week) {
        $this->ArrayWeeks[] = $week;
    }

    public function weekExists($week_id) {
        foreach ($this->ArrayWeeks as $week) {
            if ($week->getId() == $week_id) {
                return true;
            }
        }
        return false;
    }

    public function getWeekById($week_id) {
        foreach ($this->ArrayWeeks as $week) {
            if ($week->getId() == $week_id) {
                return $week;
            }
        }
        return null;
    }

    /**
     * 
     * @param type $week_tag
     * @return null|Week
     */
    public function getWeekByTag($week_tag) {
        foreach ($this->ArrayWeeks as $week) {
            if ($week->getTag() == $week_tag) {
                return $week;
            }
        }
        return null;
    }

    /**
     * 
     * @param type $file_contents
     */
    public function parseWeeks($file_contents) {
        try {
            $crawler = new Crawler();
            $crawler->addXmlContent($file_contents);
            $return_value = $crawler->filterXPath("//span");
            foreach ($return_value as $node) {
                $crawler = new Crawler();
                $crawler->add($node);
                if (!$this->weekExists($crawler->filterXPath("//title")->text())) {
                    $week = new Week();
                    $week->setDate($crawler->attr("date"));
                    $week->setId($crawler->filterXPath("//title")->text());
                    $week->setDescription($crawler->filterXPath("//description")->text());
                    $week->setTag($crawler->filterXPath("//alleventweeks")->text());
                    $this->addWeek($week);
                }
            }
        } catch (Exception $e) {
            print_r($e);
        }
    }

    /**
     * 
     * @param type $crawlter
     * @param type $node_name
     * @return string
     */
    private function parseEventNodeItems(&$crawler, $node_name) {
        $variable_value = "";
        for ($i = 0; $i < $crawler->filterXPath($node_name)->count(); $i++) {
            if ($i == $crawler->filterXPath($node_name)->count() - 1) {
                $variable_value .= $crawler->filterXPath($node_name)->getNode($i)->textContent;
            } else {
                $variable_value .= $crawler->filterXPath($node_name)->getNode($i)->textContent . "; ";
            }
        }
        return $variable_value;
    }

    /**
     * 
     * @param type $file_contents
     * @param type $formation_id
     */
    public function parseEvents($file_contents, $formation_id) {
        try {
            $crawler = new Crawler();
            $crawler->addXmlContent($file_contents);
            $return_value = $crawler->filterXPath("//event");
            foreach ($return_value as $node) {
                $crawler = new Crawler();
                $crawler->add($node);
                $event = new Event();
                $event->setFormation($formation_id);
                $event->setId($crawler->attr("id"));
//                $event->setBgColor($crawler->attr("colour"));
                $event->setWeek($crawler->filterXPath("//rawweeks")->text());
                if ($crawler->filterXPath("//room/item")->count() > 0) {
                    $event->setRoom($this->parseEventNodeItems($crawler, "//room/item"));
                }
                $event->setCategory($crawler->filterXPath("//category")->text());
                $event->setDay($crawler->filterXPath("//day")->text());

                $startDateTime = new \DateTime(str_replace("/", "-", $this->getWeekByTag($event->getWeek())->getDate()) . ' ' . $crawler->filterXPath("//starttime")->text());
                $startDateTime->modify("+" . $event->getDay() . " days");
                $endDateTime = new \DateTime(str_replace("/", "-", $this->getWeekByTag($event->getWeek())->getDate()) . ' ' . $crawler->filterXPath("//endtime")->text());
                $endDateTime->modify("+" . $event->getDay() . " days");

                $event->setStartDatetime($startDateTime);
                $event->setEndDatetime($endDateTime);
                if ($crawler->filterXPath("//group/item")->count() > 0) {
                    $event->setGroup($this->parseEventNodeItems($crawler, "//group/item"));
                }
                if ($crawler->filterXPath("//module/item")->count() > 0) {
                    $event->setModule($this->parseEventNodeItems($crawler, "//module/item"));
                }
                if ($crawler->filterXPath("//notes")->count() > 0) {
                    $event->setNote($this->parseEventNodeItems($crawler, "//notes"));
                }
                if ($crawler->filterXPath("//staff/item")->count() > 0) {
                    $event->setProfessor($this->parseEventNodeItems($crawler, "//staff/item"));
                }
                if ($crawler->filterXPath("//prettytimes")->count() > 0) {
                    $event->setTitle($this->parseEventNodeItems($crawler, "//prettytimes"));
                }
                $this->getWeekByTag($event->getWeek())->getDayById($event->getDay())->addEvent($event);
            }
        } catch (Exception $e) {
            print_r($e);
        }
    }

    /**
     * passage d'un fichier XML
     * @param type $url
     */
    public function parseAllSchedule($url) {
        $matches = array();
        preg_match("/([^.\/]*)\.xml/", $url, $matches);
        $formation_id = $matches[1];
        $file_contents = file_get_contents($url);
        $this->parseWeeks($file_contents);
        $this->parseEvents($file_contents, $formation_id);
    }

    /**
     * recupere la liste des possibilités
     * @param type $event_source
     * @param type $event_destination
     * @return type
     */
    public function getFreeEventsList($event_source, $event_destination) {
//        $tab_free_events = $this->getWeekByTag($event_destination->getWeek())
//                ->getDayById($event_destination->getDay())
//                    ->getFreeEventsList($event_destination->getStart_time(), $event_destination
//                        ->getEnd_time(), $event_destination->getFormation());
        $tab_free_events = $this->getWeekByTag($event_destination->getWeek())
                ->getWeekFreeEventsList($event_destination
                ->getStartTime(), $event_destination->getEndTime(), $event_destination->getFormation());
        return $tab_free_events;
    }

    /**
     * tester si un créneau peut etre changer vers un autre
     * @param type $event_source
     * @param type $event_destination
     */
    public function canSwapEvent($event_source, $event_destination) {
        if ($this->getWeekByTag($event_destination->getWeek())->getDayById($event_destination->getDay())
                        ->canAddEvent($event_destination->getStartTime(), $event_destination
                                ->getEndTime(), $event_destination->getFormation())) {
//            on peut ajouter un créneau à ce jour ci
            
        } else {
//            on ne peut pas ajouter un créneau (donc il faut proposer une liste de propositions)
            
        }
    }
    
    
    

}
