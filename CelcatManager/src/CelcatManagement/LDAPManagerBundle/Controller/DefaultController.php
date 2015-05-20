<?php

namespace CelcatManagement\LDAPManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name = "")
    {
        $ldapManager = $this->get('ldap_manager');
        /* @var $ldapManager \CelcatManagement\LDAPManagerBundle\LDAP\LDAPManager */
        //$results = $manager->search(Search::SCOPE_ALL, 'ou=comp,dc=example,dc=com', '(objectclass=*)');
        $recherche = $ldapManager->search("(uid=gilles.hunault)");
        
        return $this->render('CelcatManagementLDAPManagerBundle:Default:index.html.twig', 
                array(
                    'name' => $name,
                    'manager' => $ldapManager,
                    'recherche' => $recherche
                )
            );
    }
}
