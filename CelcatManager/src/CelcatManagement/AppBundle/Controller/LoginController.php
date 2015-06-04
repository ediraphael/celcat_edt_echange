<?php

namespace CelcatManagement\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use CelcatManagement\AppBundle\Form\UserCalendarsType;

class LoginController extends Controller {

    public function loginAction(Request $request, \Symfony\Component\Security\Core\Exception\AuthenticationException $exception = null) {

        if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirect($this->generateUrl('celcat_management_app_index'));
        }
//        if ($exception === null) {
//            return $this->redirect($this->generateUrl('celcat_management_app_index'));
//        }
//        $manager = $this->get('be_simple.sso_auth.factory')->getManager("admin_sso", "/");
//
//        return array(
//            'manager' => $manager,
//            'request' => $request,
//            'exception' => $exception
//        );
    }

    public function profilAction(Request $request) {
        return $this->render('CelcatManagementAppBundle:Login:profil.html.twig', array(
                    'user' => $this->getUser(),
                    'groups' => $this->get('type.group_select')->getChoices()
        ));
    }

    /**
     * Displays a form to edit an existing UserCalendars entity.
     *
     */
    public function editAction() {
        $entity = $this->getUser();
        $editForm = $this->createEditForm($entity);

        return $this->render('CelcatManagementAppBundle:Login:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a UserCalendars entity.
     *
     * @param UserCalendars $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm($entity) {
        $form = $this->createForm(new \CelcatManagement\AppBundle\Form\UserType($this->getUser()->getUsername()), $entity, array(
            'action' => $this->generateUrl('celcat_management_app_login_update'),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing UserCalendars entity.
     *
     */
    public function updateAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $entity = $this->getUser();
        /* @var $entity \CelcatManagement\AppBundle\Security\User */
        $originalUserCalendars = new \Doctrine\Common\Collections\ArrayCollection();
        foreach ($entity->getCalendars() as $calendar) {
            $originalUserCalendars->add($calendar);
        }

        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);


        if ($editForm->isValid()) {
            foreach ($originalUserCalendars as $calendar) {
                if ($entity->getCalendars()->contains($calendar) == false) {
                    $em->remove($calendar);
                }
            }
            foreach ($entity->getCalendars() as $calendar) {
                $em->persist($calendar);
            }
            $em->flush();

            return $this->redirect($this->generateUrl('profil'));
        }

        return $this->render('CelcatManagementAppBundle:Login:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
        ));
    }

}
