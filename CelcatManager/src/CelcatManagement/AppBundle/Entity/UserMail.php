<?php

namespace CelcatManagement\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserMail
 */
class UserMail extends \CelcatManagement\AppBundle\Email\Email
{
    /**
     * @var integer
     */
    private $id;

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
}
