<?php

namespace CelcatManagement\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use CelcatManagement\AppBundle\Entity\ScheduleModification;
use CelcatManagement\AppBundle\Form\ScheduleModificationType;
use CelcatManagement\AppBundle\Entity\EventModification;
use CelcatManagement\AppBundle\Entity\UserMail;
/**
 * ScheduleModification controller.
 *
 */
class ScheduleModificationController extends Controller {

    /**
     * Lists all ScheduleModification entities.
     *
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CelcatManagementAppBundle:ScheduleModification')->findAll();

        return $this->render('CelcatManagementAppBundle:ScheduleModification:index.html.twig', array(
                    'entities' => $entities,
        ));
    }

    public function sendModificationMailAction(Request $request) {
        $em = $this->getDoctrine()->getManager();
        $user = $this->getUser();
        /* @var $user \CelcatManagement\AppBundle\Security\User */
        $entities = $em->getRepository('CelcatManagementAppBundle:ScheduleModification')->findBy(array(
            'user' => $user->getUsername(),
            'mailed' => 0,
            'validated' => 1,
            'canceled' => 0
        ));
        
        if(count($entities) == 0) {
            return $this->redirect($this->generateUrl('celcat_management_app_schedulemodification'));
        }
        
        /* @var $entities ScheduleModification */
        $modelMail = $em->getRepository('CelcatManagementAppBundle:ModelMail')->findOneBy(array(
            'name' => 'schedule_modification'
        ));
        /* @var $modelMail \CelcatManagement\AppBundle\Entity\ModelMail */
        $mail = new UserMail();

        $textModification = '';
        foreach ($entities as $entity) {
            $string = $entity->getFirstEvent()->getEventTitre() .
                    ' ' . $entity->getFirstEvent()->getGroupes() .
                    ' ' . $entity->getFirstEvent()->getStartDateTimeInitial()->format('m-d-Y H:i:s') .
                    ' - ' . $entity->getFirstEvent()->getEndDateTimeInitial()->format('H:i:s') .
                    ' ==> ' . $entity->getFirstEvent()->getStartDateTimeFinal()->format('m-d-Y H:i:s') .
                    ' - ' . $entity->getFirstEvent()->getEndDateTimeFinal()->format('H:i:s');
            if ($entity->getSecondEvent() != null) {
                $string .= "<br /> " . $entity->getSecondEvent()->getEventTitre() .
                        ' ' . $entity->getSecondEvent()->getGroupes() .
                        ' ' . $entity->getSecondEvent()->getStartDateTimeInitial()->format('m-d-Y H:i:s') .
                        ' - ' . $entity->getSecondEvent()->getEndDateTimeInitial()->format('H:i:s') .
                        ' ==> ' . $entity->getSecondEvent()->getStartDateTimeFinal()->format('m-d-Y H:i:s') .
                        ' - ' . $entity->getSecondEvent()->getEndDateTimeFinal()->format('H:i:s');
            }

            $textModification .= "<br />" . $string;
        }

        $textMail = $modelMail->getBody();
        $textMail = preg_replace('/\[schedule_modification\]/', $textModification, $textMail);
        $textMail = preg_replace('/\[user_fullname\]/', $user->getFullName(), $textMail);
        
        $mail->setUser($user->getUsername());
        $mail->setFromAddress($user->getMail());
        $mail->setToAddress($this->container->getParameter('mail.celcat_admin_mail'));
        $mail->setSubject($modelMail->getSubject());
        $mail->setBody($textMail);
        $em->persist($mail);
        $em->flush();
        
        return $this->redirect($this->generateUrl('celcat_management_app_mailer_send_unsend'));
    }

    public function createFromScheduleManagerAction(Request $request) {
        $scheduleManager = new \CelcatManagement\CelcatReaderBundle\Models\ScheduleManager();
        $user = $this->getUser();
        /* @var $user \CelcatManagement\AppBundle\Security\User */
        $em = $this->getDoctrine()->getManager();

        foreach ($scheduleManager->getScheduleModifications() as $scheduleModification) {
            if ($scheduleModification->getId() == '') {
                $firstEvent = $scheduleModification->getFirstEvent();
                $secondEvent = $scheduleModification->getSecondEvent();

                $scheduleModificationEm = new ScheduleModification();
                $scheduleModificationEm->setUser($user->getUsername());

                $firstEventEm = new EventModification();
                $firstEventEm->feedByEvent($firstEvent);
                $scheduleModificationEm->setFirstEvent($firstEventEm);
                if ($secondEvent != null) {
                    $secondEventEm = new EventModification();
                    $secondEventEm->feedByEvent($secondEvent);
                    $scheduleModificationEm->setSecondEvent($secondEventEm);
                } else {
                    $scheduleModificationEm->setValidated(true);
                }
                $em->persist($scheduleModificationEm);
                $em->flush();
                $scheduleModification->setId($scheduleModificationEm->getId());
            }
        }
        $scheduleManager->save();

        return $this->redirect($this->generateUrl('celcat_management_app_schedulemodification_send_mail'));
    }

    /**
     * Creates a new ScheduleModification entity.
     *
     */
    public function createAction(Request $request) {
        $entity = new ScheduleModification();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('celcat_management_app_schedulemodification_show', array('id' => $entity->getId())));
        }

        return $this->render('CelcatManagementAppBundle:ScheduleModification:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ScheduleModification entity.
     *
     * @param ScheduleModification $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ScheduleModification $entity) {
        $form = $this->createForm(new ScheduleModificationType(), $entity, array(
            'action' => $this->generateUrl('celcat_management_app_schedulemodification_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ScheduleModification entity.
     *
     */
    public function newAction() {
        $entity = new ScheduleModification();
        $form = $this->createCreateForm($entity);

        return $this->render('CelcatManagementAppBundle:ScheduleModification:new.html.twig', array(
                    'entity' => $entity,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ScheduleModification entity.
     *
     */
    public function showAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:ScheduleModification')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ScheduleModification entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CelcatManagementAppBundle:ScheduleModification:show.html.twig', array(
                    'entity' => $entity,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ScheduleModification entity.
     *
     */
    public function editAction($id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:ScheduleModification')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ScheduleModification entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CelcatManagementAppBundle:ScheduleModification:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Creates a form to edit a ScheduleModification entity.
     *
     * @param ScheduleModification $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(ScheduleModification $entity) {
        $form = $this->createForm(new ScheduleModificationType(), $entity, array(
            'action' => $this->generateUrl('celcat_management_app_schedulemodification_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }

    /**
     * Edits an existing ScheduleModification entity.
     *
     */
    public function updateAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:ScheduleModification')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ScheduleModification entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('celcat_management_app_schedulemodification_edit', array('id' => $id)));
        }

        return $this->render('CelcatManagementAppBundle:ScheduleModification:edit.html.twig', array(
                    'entity' => $entity,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a ScheduleModification entity.
     *
     */
    public function deleteAction(Request $request, $id) {
        $em = $this->getDoctrine()->getManager();
        $scheduleManager = new \CelcatManagement\CelcatReaderBundle\Models\ScheduleManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:ScheduleModification')->find($id);
        /* @var $entity ScheduleModification */
        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ScheduleModification entity.');
        }

        $entity->setCanceled(true);
        $em->flush();
        $scheduleManager->removeScheduleModificationByEntityId($entity->getId());
        $scheduleManager->save();

        return $this->redirect($this->generateUrl('celcat_management_app_schedulemodification'));
    }

    /**
     * Creates a form to delete a ScheduleModification entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('celcat_management_app_schedulemodification_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->add('submit', 'submit', array('label' => 'Delete'))
                        ->getForm()
        ;
    }

}
