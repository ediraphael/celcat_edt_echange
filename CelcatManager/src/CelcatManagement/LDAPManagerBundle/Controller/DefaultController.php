<?php

namespace CelcatManagement\LDAPManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name = "")
    {
        return $this->render('CelcatManagementLDAPManagerBundle:Default:index.html.twig', array('name' => $name));
    }
}
