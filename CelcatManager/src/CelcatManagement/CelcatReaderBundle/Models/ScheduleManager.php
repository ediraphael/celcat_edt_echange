<?php

namespace CelcatManagement\CelcatReaderBundle\Models;

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
    
    public function addWeek(Week $week)
    {
        $this->tab_weeks[] = $week;
    }


}

