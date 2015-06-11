<?php

namespace CelcatManagement\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserMail
 */
class UserMail extends Mail
{
    /**
     * @var integer
     */
    private $id;
    
    /**
     * @var boolean
     */
    private $sended;
    
    /**
     * @var \DateTime
     */
    private $sendDate = 'CURRENT_TIMESTAMP';

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
     * Set sendDate
     *
     * @param \DateTime $sendDate
     * @return UserMail
     */
    public function setSendDate($sendDate)
    {
        $this->sendDate = $sendDate;

        return $this;
    }

    /**
     * Get sendDate
     *
     * @return \DateTime 
     */
    public function getSendDate()
    {
        return $this->sendDate;
    }

    /**
     * Set sended
     *
     * @param boolean $sended
     * @return UserMail
     */
    public function setSended($sended)
    {
        $this->sended = $sended;

        return $this;
    }

    /**
     * Get sended
     *
     * @return boolean 
     */
    public function getSended()
    {
        return $this->sended;
    }
}
