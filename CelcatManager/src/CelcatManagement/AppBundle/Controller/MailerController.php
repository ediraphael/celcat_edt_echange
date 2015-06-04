<?php

namespace CelcatManagement\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CelcatManagement\AppBundle\Form\UserMailType;
use CelcatManagement\AppBundle\Entity\UserMail;

class MailerController extends Controller {

    public function indexAction(Request $request) {
        $email = new UserMail();
        $user = $this->getUser();
        /* @var $user \CelcatManagement\AppBundle\Security\User */

        $form = $this->createForm(new UserMailType($user->getMail()), $email, array(
            'action' => $this->generateUrl('celcat_management_app_mailer_send'),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Envoyer'));

        return $this->render('CelcatManagementAppBundle:Mailer:index.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    public function sendAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $email = new UserMail();
        $user = $this->getUser();
        /* @var $user \CelcatManagement\AppBundle\Security\User */

        $form = $this->createForm(new UserMailType($user->getMail()), $email, array(
            'action' => $this->generateUrl('celcat_management_app_mailer_send'),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Envoyer'));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $message = \Swift_Message::newInstance()
                    ->setSubject($email->getSubject())
                    ->setFrom($email->getFromAddress())
                    ->setTo($email->getToAddress())
                    ->setBody($email->getBody())
            ;
            $failed = array();
            $result = $this->get('mailer')->send($message, $failed);
            $em->persist($email);
            $em->flush();
        }

        return $this->render('CelcatManagementAppBundle:Mailer:index.html.twig', array(
                    'form' => $form->createView()
        ));
    }

}
