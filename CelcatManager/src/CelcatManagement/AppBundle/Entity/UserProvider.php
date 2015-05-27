<?php

namespace CelcatManagement\AppBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use BeSimple\SsoAuthBundle\Security\Core\User\UserFactoryInterface;
use CelcatManagement\AppBundle\Entity\User;

class UserProvider implements UserProviderInterface, UserFactoryInterface {

    /**
     * @var array
     */
    private $roles;
    
    /**
     *
     * @var \CelcatManagement\LDAPManagerBundle\LDAP\LDAPManager
     */
    private $ldapManager;
 
    /**
     * Constructor.
     *
     * @param array $roles An array of roles
     */
    public function __construct(array $roles = array(), \CelcatManagement\LDAPManagerBundle\LDAP\LDAPManager $ldapManager)
    {
        $this->roles = $roles;
        $this->ldapManager = $ldapManager;
    }
    
    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $this->roles = ["ROLE_USER"];
        return $this->spawnUser($username);
    }

    public function createUser($username, array $roles, array $attributes) 
    {
        $user = new User($username, null, null, $roles);
        $userLDAP = $this->ldapManager->searchUser($user->getUsername());
        $user->hydrateWithLDAP($userLDAP);
        return $user; 
    }
    
    /**
     * Spawns a new user with given username.
     *
     * @param string $username
     *
     * @return \Symfony\Component\Security\Core\User\User
     */
    private function spawnUser($username)
    {
        $user = new User($username, null, null, $this->roles);
        $userLDAP = $this->ldapManager->searchUser($user->getUsername());
        $user->hydrateWithLDAP($userLDAP);
        return $user; 
    }

    /**
     * {@inheritDoc}
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
        }
 
        return $this->spawnUser($user->getUsername());
    }

    public function supportsClass($class) {
        return $this->getEntityName() === $class || is_subclass_of($class, $this->getEntityName());
    }

}
