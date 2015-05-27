<?php

namespace CelcatManagement\LDAPManagerBundle\LDAP;

use CelcatManagement\LDAPManagerBundle\LDAP\Core\Manager;
use CelcatManagement\LDAPManagerBundle\LDAP\Platform\Native\Driver;
use Toyota\Component\Ldap\Platform\Native\Search;

/**
 * Description of LDAPManager
 *
 * @author raphael
 */
class LDAPManager {

    /**
     * Host du LDAP
     * @var string 
     */
    private $hostname;

    /**
     * Base dn
     * @var string 
     */
    private $dc;

    /**
     * Base ou
     * @var string 
     */
    private $ou;

    /**
     * Utilisateur
     * @var string 
     */
    private $user;

    /**
     * Mot de passe
     * @var string 
     */
    private $password;

    /**
     * Manager du LDAP
     * @var Manager 
     */
    private $manager;

    function __construct($hostname, $dc, $ou, $user, $password) {
        //ldapsearch -x -h ldap.univ-angers.fr -b ou=people,dc=univ-angers,dc=fr 
        //-D uid=LOGIN,ou=people,dc=univ-angers,dc=fr -W "(boolean(filter)(filter))"
        $this->hostname = $hostname;
        $this->dc = $dc;
        $this->ou = $ou;
        $this->user = $user;
        $this->password = $password;

        $params = array(
            'hostname' => $hostname,
            'base_dn' => $ou . ',' . $dc
        );
        $this->manager = new Manager($params, new Driver());

        try {
            $this->manager->connect();
        } catch (ConnectionException $e) {
            throw new Exception("Erreur de connection au serveur LDAP");
        }

        try {
            $this->manager->bind('uid='.$user . ',' . $ou . ',' . $dc, $password);
        } catch (BindingException $e) {
            throw new Exception("Erreur de d'identification au serveur LDAP");
        }
    }
    
    function searchUser($username = "") {
        if($username == "" || $username == null) {
            return null;
        }
        return $this->search("(uid=$username)");
    }
    
    function search($filter = "") {
        return $this->manager->search($this->ou . ',' . $this->dc, $filter);
    }

    function getHostname() {
        return $this->hostname;
    }

    function getDc() {
        return $this->dc;
    }

    function getOu() {
        return $this->ou;
    }

    function getUser() {
        return $this->user;
    }

    function getPassword() {
        return $this->password;
    }

    function getManager() {
        return $this->manager;
    }

    function setHostname($hostname) {
        $this->hostname = $hostname;
    }

    function setDc($dc) {
        $this->dc = $dc;
    }

    function setOu($ou) {
        $this->ou = $ou;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function setPassword($password) {
        $this->password = $password;
    }

    function setManager(Manager $manager) {
        $this->manager = $manager;
    }

}
