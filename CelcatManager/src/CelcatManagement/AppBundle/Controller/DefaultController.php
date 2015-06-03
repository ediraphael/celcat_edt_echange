<?php

namespace CelcatManagement\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

    public function indexAction() {
        $user = $this->getUser();
        /* @var $user \CelcatManagement\AppBundle\Security\User */
        $url = $this->container->getParameter('celcat.url').$this->container->getParameter('celcat.teacherPath');
        $calendarFileSource = $url.$user->getIdentifier().'.xml';
        return $this->render('CelcatManagementAppBundle:Default:index.html.twig', array(
            'calendar_file_source' => $calendarFileSource
        ));
    }

}
