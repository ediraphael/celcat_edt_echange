<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

use Symfony\Component\DomCrawler\Crawler;

class ScheduleManager {

    /**
     *
     * @var Week[] 
     */
    private $arrayWeeks;
    
    /**
     *
     * @var ScheduleModification[] 
     */
    private $scheduleModifications;

    function __construct() {
        if (isset($_SESSION['schedulerManager'])) {
            $scheduleur = unserialize($_SESSION['schedulerManager']);
            $this->arrayWeeks = $scheduleur->getArrayWeeks();
            $this->scheduleModifications = $scheduleur->getScheduleModifications();
        } else {
            $this->arrayWeeks = array();
            $this->scheduleModifications = array();
            $_SESSION['schedulerManager'] = serialize($this);
        }
    }

    public function getArrayWeeks() {
        return $this->arrayWeeks;
    }

    public function setArrayWeeks($arrayWeeks) {
        $this->arrayWeeks = $arrayWeeks;
        $_SESSION['schedulerManager'] = serialize($this);
    }

    public function addWeek($week) {
        $this->arrayWeeks[] = $week;
        $_SESSION['schedulerManager'] = serialize($this);
    }

    public function freeWeeks() {
        $this->arrayWeeks = array();
    }

    public function weekExists($week_id) {
        foreach ($this->arrayWeeks as $week) {
            if ($week->getId() == $week_id) {
                return true;
            }
        }
        return false;
    }

    public function getWeekById($week_id) {
        foreach ($this->arrayWeeks as $week) {
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
        foreach ($this->arrayWeeks as $week) {
            if ($week->getTag() == $week_tag) {
                return $week;
            }
        }
        return null;
    }
    
    public function getScheduleModifications() {
        return $this->scheduleModifications;
    }

    public function setScheduleModifications(array $scheduleModifications) {
        $this->scheduleModifications = $scheduleModifications;
        return $this;
    }
    
    public function addScheduleModification(ScheduleModification $scheduleModification) {
        $this->scheduleModifications[$scheduleModification->getFirstEvent()->getId()] = $scheduleModification;
    }
    
    public function removeScheduleModification(ScheduleModification $scheduleModifications) {
        unset($this->scheduleModifications[$scheduleModifications->getFirstEvent()->getId()]);
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
                $event->addFormation($formation_id);
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
                    $professors = explode(";", $this->parseEventNodeItems($crawler, "//staff/item"));
                    foreach ($professors as $professor) {
                        $event->addProfessor($professor);
                    }
                }
                if ($crawler->filterXPath("//prettytimes")->count() > 0) {
                    $event->setTitle($this->parseEventNodeItems($crawler, "//prettytimes"));
                }
                $this->getWeekByTag($event->getWeek())->getDayById($event->getDay())->addEvent($event);
            }
        } catch (Exception $e) {
            print_r($e);
        }
        $_SESSION['schedulerManager'] = serialize($this);
    }

    /**
     * passage d'un fichier XML
     * @param type $url
     */
    public function parseAllSchedule($url) {
        $matches = array();
        preg_match("/([^.\/]*)\.xml/", $url, $matches);
        if (count($matches) > 0) {
            $formation_id = $matches[1];
        } else {
            preg_match("/\=([0-9]+)&/", $url, $matches);
            $formation_id = $matches[1];
        }
        if ($formation_id[0] == 'g') {
            $file_contents = file_get_contents($url);
            $this->parseWeeks($file_contents);
            $this->parseEvents($file_contents, $formation_id);
        }
        $_SESSION['schedulerManager'] = serialize($this);
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
                ->getStartTime(), $event_destination->getEndTime());
        return $tab_free_events;
    }

    /**
     * @param Event $eventSource
     * @param Event $eventDestination
     */
    public function swapEvent(Event $eventSource, Event $eventDestination) {
        if (!$eventSource->hasReplacementEvent() && !$eventDestination->hasReplacementEvent()) {
            $newEventSource = clone $eventSource;
            $newEventDestination = clone $eventDestination;
            
            $startSource = $eventSource->getStartDatetime();
            $startDestinaton = $eventDestination->getStartDatetime();
            $durationSource = $eventDestination->getStartDatetime()->diff($eventDestination->getEndDatetime(), true);
            $durationDestination = $eventSource->getStartDatetime()->diff($eventSource->getEndDatetime(), true);
            
            $newEventSource->setStartDatetime($startDestinaton);
            $newEndDateTimeSource = clone $startDestinaton;
            $newEndDateTimeSource->add($durationDestination);
            $newEventSource->setEndDatetime($newEndDateTimeSource);
            
            $newEventDestination->setStartDatetime($startSource);
            $newEndDateTimeDestination = clone $startSource;
            $newEndDateTimeDestination->add($durationSource);
            $newEventDestination->setEndDatetime($newEndDateTimeDestination);          
            
            $eventSource->replaceBy($newEventSource);
            $eventDestination->replaceBy($newEventDestination);
            
            
            $scheduleModification = new ScheduleModification();
            $scheduleModification->setSwapModification($eventSource, $eventDestination);
            $this->addScheduleModification($scheduleModification);

            $_SESSION['schedulerManager'] = serialize($this);
            return true;
        } else {
            return false;
        }
    }

    /**
     * 
     * @param type $event_source
     * @param type $event_destination
     * @return boolean
     */
    public function canSwapEvent(&$event_source, &$event_destination, $user) {
        $array_formations_ids = array();
        foreach ($this->getWeekByTag($event_destination->getWeek())->getDayById($event_destination->getDay())->getArrayEvents() as $event) {
            if ($event->getId() == $event_destination->getId()) {
                $array_formations_ids[] = $event->getFormations();
            }
        }
        foreach ($array_formations_ids as $formation_ids) {
            foreach ($formation_ids as $formation_id) {
                $duree_event_destination = gmdate('H:i', strtotime($event_destination->getEndTime()) - strtotime($event_destination->getStartTime()));
                $hours = explode(":", $duree_event_destination)[0];
                $minutes = explode(":", $duree_event_destination)[1];
                $convert = strtotime("+$hours hours", strtotime($event_source->getStartTime()));
                $convert = strtotime("+$minutes minutes", $convert);
                $calculated_end_time = date('H:i', $convert);
                if (!$this->getWeekByTag($event_source->getWeek())->getDayById($event_source->getDay())
                                ->canAddEvent($event_source->getId(), $event_source->getStartTime(), $calculated_end_time, $formation_id, $user)) {
                    $event_source->setBgColor("orange");
                    $event_destination->setBgColor("");
                    $_SESSION['schedulerManager'] = serialize($this);
                    return false;
                }
            }
        }
        $event_source->setBgColor("orange");
        $event_destination->setBgColor("green");
        $_SESSION['schedulerManager'] = serialize($this);
        return true;
    }
    
    public function save() {
        $_SESSION['schedulerManager'] = serialize($this);
    }

}
