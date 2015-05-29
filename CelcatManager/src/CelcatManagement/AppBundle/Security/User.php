<?php

namespace CelcatManagement\AppBundle\Security;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\EquatableInterface;

class User implements UserInterface {

    private $username;
    private $password;
    private $salt;
    private $roles;
    
    private $mail;
    private $fullName;
    private $group;
    private $groupName;

    public function __construct($username, $password, $salt, array $roles) {
        $this->username = $username;
        $this->password = $password;
        $this->salt = $salt;
        $this->roles = $roles;
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
        if($userLDAP != null) {
            $this->setFullName($userLDAP->current()->get('cn')->getValues()[0]);
            $this->setMail($userLDAP->current()->get('mail')->getValues()[0]);
            $this->setGroup($userLDAP->current()->get('auaPopulation')->getValues()[0]);
            $this->setGroupName($userLDAP->current()->get('title')->getValues()[0]);
        }
    }
}
