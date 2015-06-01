<?php

namespace CelcatManagement\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserCalendars
 */
class UserCalendars
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $calendarFile;

    /**
     * @var string
     */
    private $calendarName;

    /**
     * @var string
     */
    private $calendarComment;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return UserCalendars
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set calendarFile
     *
     * @param string $calendarFile
     * @return UserCalendars
     */
    public function setCalendarFile($calendarFile)
    {
        $this->calendarFile = $calendarFile;

        return $this;
    }

    /**
     * Get calendarFile
     *
     * @return string 
     */
    public function getCalendarFile()
    {
        return $this->calendarFile;
    }

    /**
     * Set calendarName
     *
     * @param string $calendarName
     * @return UserCalendars
     */
    public function setCalendarName($calendarName)
    {
        $this->calendarName = $calendarName;

        return $this;
    }

    /**
     * Get calendarName
     *
     * @return string 
     */
    public function getCalendarName()
    {
        return $this->calendarName;
    }

    /**
     * Set calendarComment
     *
     * @param string $calendarComment
     * @return UserCalendars
     */
    public function setCalendarComment($calendarComment)
    {
        $this->calendarComment = $calendarComment;

        return $this;
    }

    /**
     * Get calendarComment
     *
     * @return string 
     */
    public function getCalendarComment()
    {
        return $this->calendarComment;
    }
    
    public function __toString() {
        return $this->calendarName;
    }

}
