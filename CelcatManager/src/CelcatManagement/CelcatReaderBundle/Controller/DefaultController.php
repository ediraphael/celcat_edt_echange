<?php

namespace CelcatManagement\CelcatReaderBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('CelcatManagementCelcatReaderBundle:Default:index.html.twig', array('name' => $name));
    }
}
