<?php

namespace CelcatManagement\AppBundle\Entity;

/**
 * Description of Email
 *
 * @author raphael
 */
class Mail {

    /**
     * From adress
     * @var string 
     */
    private $fromAddress;

    /**
     * To adress
     * @var string
     */
    private $toAddress;

    /**
     * Subject
     * @var string 
     */
    private $subject;

    /**
     * Body
     * @var string 
     */
    private $body;

        
    public function getFromAddress() {
        return $this->fromAddress;
    }

    public function getToAddress() {
        return $this->toAddress;
    }

    public function getSubject() {
        return $this->subject;
    }

    public function getBody() {
        return $this->body;
    }

    public function setFromAddress($fromAddress) {
        $this->fromAddress = $fromAddress;
        return $this;
    }

    public function setToAddress($toAddress) {
        $this->toAddress = $toAddress;
        return $this;
    }

    public function setSubject($subject) {
        $this->subject = $subject;
        return $this;
    }

    public function setBody($body) {
        $this->body = $body;
        return $this;
    }

}
