<?php

namespace CelcatManagement\LDAPManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name = "")
    {
        $ldapManager = $this->get('ldap_manager');
        /* @var $ldapManager \CelcatManagement\LDAPManagerBundle\LDAP\LDAPManager */
        //$recherche = $ldapManager->searchUser("adamfouchard");
        $recherche = $ldapManager->getUserByFullName("ABELARD,Karine");
//        $recherches = array();
//        while($recherche->valid()) {
//            $recherches[] = clone $recherche;
//            $recherche->next();
//        }
//        /* @var $recherche \CelcatManagement\LDAPManagerBundle\LDAP\Core\SearchResult */
        //CHASLE,Christelle
        //ADAM FOUCHARD,Frederique
        return $this->render('CelcatManagementLDAPManagerBundle:Default:index.html.twig', 
                array(
                    'name' => $name,
                    'recherche' => $recherche
                )
            );
    }
}
