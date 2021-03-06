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
     * @var Event[] 
     */
    private $events;

    /**
     *
     * @var ScheduleModification[] 
     */
    private $scheduleModifications;

    /**
     *  
     * @var \Doctrine\Common\Collections\ArrayCollection 
     */
    private $loadedFormations;

    function __construct() {
        if (isset($_SESSION['schedulerManager'])) {
            $scheduleur = unserialize($_SESSION['schedulerManager']);
            $this->arrayWeeks = $scheduleur->getArrayWeeks();
            $this->scheduleModifications = $scheduleur->getScheduleModifications();
            $this->loadedFormations = $scheduleur->getLoadedFormations();
            $this->events = $scheduleur->getEvents();
        } else {
            $this->arrayWeeks = array();
            $this->events = array();
            $this->scheduleModifications = array();
            $this->loadedFormations = new \Doctrine\Common\Collections\ArrayCollection();
            $this->save();
        }
    }

    public function getArrayWeeks() {
        return $this->arrayWeeks;
    }

    public function setArrayWeeks($arrayWeeks) {
        $this->arrayWeeks = $arrayWeeks;
        $this->save();
    }

    public function addWeek($week) {
        $this->arrayWeeks[] = $week;
        $this->save();
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

    /**
     * 
     * @param type $weeknumber
     * @return null|Week
     */
    public function getWeekByWeeknumber($weeknumber) {
        foreach ($this->arrayWeeks as $week) {
            if ($week->getWeeknumber() == $weeknumber) {
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
        if (isset($this->scheduleModifications[$scheduleModification->getFirstEvent()->getId()]) && $this->scheduleModifications[$scheduleModification->getFirstEvent()->getId()] != '') {
            if ($scheduleModification->isSwapModification()) {
                $this->scheduleModifications[$scheduleModification->getFirstEvent()->getId()] = $scheduleModification;
            } elseif ($scheduleModification->isDropModification() && $scheduleModification->isResizeModification()) {
                $this->scheduleModifications[$scheduleModification->getFirstEvent()->getId()]->setFirstEvent($scheduleModification->getFirstEvent());
            }
        } else {
            $this->scheduleModifications[$scheduleModification->getFirstEvent()->getId()] = $scheduleModification;
        }
    }

    public function removeScheduleModification(ScheduleModification $scheduleModification) {
        unset($this->scheduleModifications[$scheduleModification->getFirstEvent()->getId()]);
    }

    public function removeScheduleModificationById($eventId) {
        foreach ($this->scheduleModifications as $scheduleModification) {
            if ($scheduleModification->getFirstEvent()->getId() == $eventId) {
                $scheduleModification->getFirstEvent()->deleteReplacementEvent();
                if ($scheduleModification->getSecondEvent() != null && $scheduleModification->getSecondEvent() != '') {
                    $scheduleModification->getSecondEvent()->deleteReplacementEvent();
                }
                $this->removeScheduleModification($scheduleModification);
            }
        }
    }

    public function removeScheduleModificationByEntityId($id) {
        foreach ($this->scheduleModifications as $scheduleModification) {
            if ($scheduleModification->getId() == $id) {
                $this->removeScheduleModificationById($scheduleModification->getFirstEvent()->getId());
            }
        }
    }

    public function getLoadedFormations() {
        return $this->loadedFormations;
    }

    public function setLoadedFormations(\Doctrine\Common\Collections\ArrayCollection $loadedFormations) {
        $this->loadedFormations = $loadedFormations;
        return $this;
    }

    public function addLoadedFormation($loadedFormation) {
        $this->loadedFormations->add($loadedFormation);
        return $this;
    }

    public function getEvents() {
        return $this->events;
    }

    public function setEvents(array $events) {
        $this->events = $events;
        return $this;
    }

    public function addEvent(Event $event) {
        if (isset($this->events[$event->getId()]) && $this->events[$event->getId()] != '') {
            $this->events[$event->getId()]->addFormation($event->getFormations());
        } else {
            $this->events[$event->getId()] = $event;
        }
    }

    public function removeEvent(Event $event) {
        unset($this->events[$event->getId()]);
    }

    public function getEventById($eventId) {
        if (isset($this->events[$eventId])) {
            return $this->events[$eventId];
        }
        return null;
    }

    /**
     * 
     * @param type $file_contents
     */
    public function parseWeeks($file_contents) {
        try {
            $todayDate = new \DateTime();
            $crawler = new Crawler();
            $crawler->addXmlContent($file_contents);
            $return_value = $crawler->filterXPath("//span");
            foreach ($return_value as $node) {
                $crawler = new Crawler();
                $crawler->add($node);
                if (!$this->weekExists($crawler->filterXPath("//title")->text())) {
                    $week = new Week();
                    $week->setDate($crawler->attr("date"));
                    $date = new \DateTime(str_replace("/", "-", $week->getDate()));
                    $week->setWeeknumber($date->format("W"));
                    $week->setId($crawler->filterXPath("//title")->text());
                    $week->setDescription($crawler->filterXPath("//description")->text());
                    $week->setTag($crawler->filterXPath("//alleventweeks")->text());

                    $weekDate = new \DateTime(str_replace('/', '-', $week->getDate()));
                    $diff = $weekDate->diff($todayDate);
                    /* @var $diff \DateInterval */
                    if ($diff->days * ($diff->invert ? 1 : -1) > -8) {
                        $this->addWeek($week);
                    }
                }
            }
            $this->save();
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
                if ($this->getWeekByTag($crawler->filterXPath("//rawweeks")->text()) != null) {
                    $event = new Event();
                    $event->addFormation($formation_id);
                    $event->setId($crawler->attr("id"));
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
                            $event->addProfessor(trim($professor));
                        }
                    }
                    if ($crawler->filterXPath("//prettytimes")->count() > 0) {
                        $event->setTitle($this->parseEventNodeItems($crawler, "//prettytimes"));
                    }
                    $event->delete();
                    $this->addEvent($event);
                }
            }
        } catch (Exception $e) {
            print_r($e);
        }
        $this->save();
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
        $file_contents = file_get_contents($url);
        $this->parseWeeks($file_contents);
        $this->parseEvents($file_contents, $formation_id);
        $this->addLoadedFormation($formation_id);
        $this->save();
    }

    /**
     * recupere la liste des possibilités
     * @param type $event_source
     * @param type $event_destination
     * @return type
     */
    public function getFreeEventsList($event_source, $event_destination) {
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

        $newEventSource = clone $eventSource;
        $newEventDestination = clone $eventDestination;

        $startSource = clone $eventSource->getStartDatetime();
        $startDestinaton = clone $eventDestination->getStartDatetime();
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

        $newEventSource->swaped();
        $newEventDestination->swaped();
        $newEventSource->deleteReplacementEvent();
        $eventSource->replaceBy($newEventSource);
        $eventDestination->replaceBy($newEventDestination);


        $scheduleModification = new ScheduleModification();
        $scheduleModification->setSwapModification($eventSource, $eventDestination);
        $this->addScheduleModification($scheduleModification);

        $this->save();
        return true;
    }

    /**
     * 
     * @param Event $eventSource
     * @param Event $eventDestination
     * @return boolean
     */
    public function canSwapEvent(Event &$eventSource, Event &$eventDestination, $user, \Symfony\Component\DependencyInjection\Container $container) {
        //On charge des données initial
        $urlStudendPath = $container->getParameter('celcat.url') . $container->getParameter('celcat.studentPath');
        $urlTeacherPath = $container->getParameter('celcat.url') . $container->getParameter('celcat.teacherPath') . $container->getParameter('celcat.teacherFileGet');
        $todayArg = $container->getParameter('celcat.teacherFileArgumentKey') . '=' . mktime(0, 0, 0, date("m"), date("d"), date("Y"));
        $fileArg = $container->getParameter('celcat.teacherFileArgumentId');
        $ldapManager = $container->get('ldap_manager');
        $todayDate = new \DateTime();
        /* @var $ldapManager \CelcatManagement\LDAPManagerBundle\LDAP\LDAPManager */

        //Si l'emploi du temps de destination n'a pas de prof, on le retire
        if ($eventDestination->getProfessors()->isEmpty()) {
            return false;
        }

        if ($eventDestination->getStartDatetime() < $todayDate) {
            return false;
        }

        if ($eventSource->isSwaped()) {
            return false;
        }

        //On récupère les formations
        $eventDestinationFormations = $eventDestination->getFormations();
        $eventSourceFormations = $eventSource->getFormations();

        //Ainsi que les nom de formations pour les profs
        $eventDestinationProfessors = $eventDestination->getProfessors();
        $professorFormations = array();
        foreach ($eventDestinationProfessors as $professors) {
            $userProfessor = $ldapManager->getUserByFullName($professors);
            if ($userProfessor != null && $userProfessor->getIdentifier() != '') {
                $professorFormations[] = $userProfessor->getIdentifier();
            }
        }

        // On charge toutes les formations necessaire
        $formations = array_merge($eventDestinationFormations->toArray(), $eventSourceFormations->toArray(), $professorFormations);
        foreach ($formations as $formation) {
            if (!$this->loadedFormations->contains($formation)) {
                $url = '';
                if ($formation[0] == 'g') {
                    $url = $urlStudendPath . $formation . '.xml';
                } else {
                    $url = $urlTeacherPath . '?' . $fileArg . '=' . $formation . '&' . $todayArg;
                }
                $this->parseAllSchedule($url);
            }
        }

        $newEventSource = clone $eventSource;
        $newEventDestination = clone $eventDestination;

        $startSource = clone $eventSource->getStartDatetime();
        $startDestinaton = clone $eventDestination->getStartDatetime();
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


//        if ($newEventDestination->isEventCrossed($newEventSource)) {
//            return false;
//        }

        foreach ($this->getEvents() as $event) {
            if ($event->getId() != $newEventSource->getId() && $event->getId() != $newEventDestination->getId()) {
                if ($event->getStartDatetime()->format('z') == $newEventDestination->getStartDatetime()->format('z')) {
                    if ($event->containsFormations($newEventDestination->getFormations())) {
                        if ($event->isEventCrossed($newEventDestination)) {
                            return false;
                        }
                    }
                }
            }
        }


        foreach ($this->getEvents() as $event) {
            if ($event->getId() != $newEventSource->getId() && $event->getId() != $newEventDestination->getId()) {
                if ($event->getStartDatetime()->format('z') == $newEventSource->getStartDatetime()->format('z')) {
                    if ($event->containsFormations($newEventSource->getFormations())) {
                        if ($event->isEventCrossed($newEventSource)) {
                            return false;
                        }
                    }
                }
            }
        }

        return true;
    }

    public function save() {
        $_SESSION['schedulerManager'] = serialize($this);
    }

}
