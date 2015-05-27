<?php

namespace CelcatManagement\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function indexAction($name) {
        return $this->render('CelcatManagementAppBundle:Default:index.html.twig', array('name' => $name));
    }

    public function index2Action() {
        return $this->render('CelcatManagementAppBundle:Default:index.html.twig', array('name' => "toto"));
    }
}
