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

    public function loginAction(\Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\Security\Core\Exception\AuthenticationException $exception = null) {

        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('celcat_management_app_index'));
        }
        if ($exception === null) {
            return $this->redirect($this->generateUrl('celcat_management_app_index'));
        }
        $manager = $this->get('be_simple.sso_auth.factory')->getManager("admin_sso", "/");

        return array(
            'manager' => $manager,
            'request' => $request,
            'exception' => $exception
        );
    }
}
