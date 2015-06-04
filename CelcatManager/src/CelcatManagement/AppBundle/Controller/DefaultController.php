<?php

namespace CelcatManagement\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function indexAction() {
        $user = $this->getUser();
        /* @var $user \CelcatManagement\AppBundle\Security\User */
        $url = $this->container->getParameter('celcat.url') . $this->container->getParameter('celcat.teacherPath') . $this->container->getParameter('celcat.teacherFileGet');
        $argument = '?' . $this->container->getParameter('celcat.teacherFileArgumentId') . '=' . $user->getIdentifier() .
                '&' . $this->container->getParameter('celcat.teacherFileArgumentKey') . '=' . mktime(0, 0, 0, date("m"), date("d"), date("Y"));



        $calendarFileSource = $url . $argument;
        return $this->render('CelcatManagementAppBundle:Default:index.html.twig', array(
                    'calendar_file_source' => $calendarFileSource
        ));
    }

}
