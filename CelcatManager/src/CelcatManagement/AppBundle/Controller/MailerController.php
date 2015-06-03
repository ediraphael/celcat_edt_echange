<?php

namespace CelcatManagement\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CelcatManagement\AppBundle\Form\EmailType;
use CelcatManagement\AppBundle\Email\Email;

class MailerController extends Controller {

    public function indexAction(Request $request) {
        $email = new Email();
        $user = $this->getUser();
        /* @var $user \CelcatManagement\AppBundle\Security\User */

        $form = $this->createForm(new EmailType($user->getMail()), $email, array(
            'action' => $this->generateUrl('celcat_management_app_mailer_send'),
            'method' => 'POST',
        ));
        $form->add('submit', 'submit', array('label' => 'Envoyer'));

        return $this->render('CelcatManagementAppBundle:Mailer:index.html.twig', array(
                    'form' => $form->createView()
        ));
    }

    public function sendAction(Request $request) {
        $email = new Email();
        $user = $this->getUser();
        /* @var $user \CelcatManagement\AppBundle\Security\User */

        $form = $this->createForm(new EmailType($user->getMail()), $email, array(
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
            $this->get('mailer')->send($message);
            echo 'OK<br/>';
        }

        return $this->render('CelcatManagementAppBundle:Mailer:index.html.twig', array(
                    'form' => $form->createView()
        ));
    }

}
