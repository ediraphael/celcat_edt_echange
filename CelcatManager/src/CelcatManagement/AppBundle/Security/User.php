<?php

namespace CelcatManagement\AppBundle\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface {

    /**
     * Username
     * @var string 
     */
    private $username;

    /**
     * Password (inutilisé)
     * @var string 
     */
    private $password;

    /**
     * Salt pour le password (inutilisé)
     * @var string 
     */
    private $salt;

    /**
     * Roles utilisateurs
     * @var string[] 
     */
    private $roles;

    /**
     *  Identifiant utilisateur
     * @var string 
     */
    private $gidNumber;

    /**
     * Mail utilisateur
     * @var string
     */
    private $mail;

    /**
     * Nom complet utilisateur
     * @var string
     */
    private $fullName;

    /**
     * Code groupe utilisateur
     * @var string
     */
    private $group;

    /**
     * Nom groupe utilisateur
     * @var string 
     */
    private $groupName;

    /**
     * Calendrier utilisateur
     * @var \Doctrine\Common\Collections\Collection
     */
    private $calendars;

    public function __construct($username, $password, $salt, array $roles) {
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->roles = $roles;
        $this->calendars = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getRoles() {
        return $this->roles;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function getUsername() {
        return $this->username;
    }

    public function setRoles($roles) {
        $this->roles = $roles;
    }

    public function setPassword($password) {
        $this->password = $password;
    }

    public function setSalt($salt) {
        $this->salt = $salt;
    }

    public function setUsername($username) {
        $this->username = $username;
    }

    function getGidNumber() {
        return $this->gidNumber;
    }

    function setGidNumber($gidNumber) {
        $this->gidNumber = $gidNumber;
    }

    public function getMail() {
        return $this->mail;
    }

    public function getFullName() {
        return $this->fullName;
    }

    public function getGroup() {
        return $this->group;
    }

    public function getGroupName() {
        return $this->groupName;
    }

    public function setMail($mail) {
        $this->mail = $mail;
    }

    public function setFullName($fullName) {
        $this->fullName = $fullName;
    }

    public function setGroup($group) {
        $this->group = $group;
    }

    public function setGroupName($groupName) {
        $this->groupName = $groupName;
    }

    public function getIdentifier() {
        $matches = array();
        preg_match('/[0-9]0*([0-9]*)/', $this->getGidNumber(), $matches);
        if (count($matches) > 1) {
            return $matches[1];
        }
        return null;
    }

    public function getCalendars() {
        return $this->calendars;
    }

    public function setCalendars(\Doctrine\Common\Collections\ArrayCollection $calendars) {
        $this->calendars = $calendars;

        return $this;
    }

    public function addCalendar(\CelcatManagement\AppBundle\Entity\UserCalendars $calendar) {
        $this->calendars[] = $calendar;

        return $this;
    }

    public function removeCalendar(\CelcatManagement\AppBundle\Entity\UserCalendars $calendar) {
        $this->calendars->removeElement($calendar);
        
        return $this;
    }

    public function eraseCredentials() {
        
    }

    public function equals(UserInterface $user) {
        if (!$user instanceof User) {
            return false;
        }
        if ($this->password !== $user->getPassword()) {
            return false;
        }
        if ($this->getSalt() !== $user->getSalt()) {
            return false;
        }
        if ($this->username !== $user->getUsername()) {
            return false;
        }
        return true;
    }

    public function hydrateWithLDAP(\CelcatManagement\LDAPManagerBundle\LDAP\Core\SearchResult $userLDAP) {
        if ($userLDAP != null) {
            $this->setGidNumber($userLDAP->current()->get('gidNumber')->getValues()[0]);
            $this->setFullName($userLDAP->current()->get('cn')->getValues()[0]);
            $this->setMail($userLDAP->current()->get('mail')->getValues()[0]);
            $this->setGroup($userLDAP->current()->get('auaPopulation')->getValues()[0]);
            $this->setGroupName($userLDAP->current()->get('title')->getValues()[0]);
        }
    }

}
