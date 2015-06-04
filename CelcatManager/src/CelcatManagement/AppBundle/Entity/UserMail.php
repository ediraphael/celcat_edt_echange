<?php

namespace CelcatManagement\AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserMail
 */
class UserMail extends Mail
{
    /**
     * @var \DateTime
     */
    private $sendDate = 'CURRENT_TIMESTAMP';

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
