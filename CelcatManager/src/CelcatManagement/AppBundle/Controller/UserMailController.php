<?php

namespace CelcatManagement\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use CelcatManagement\AppBundle\Entity\UserMail;
use CelcatManagement\AppBundle\Form\UserMailType;

/**
 * UserMail controller.
 *
 */
class UserMailController extends Controller
{

    /**
     * Lists all UserMail entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CelcatManagementAppBundle:UserMail')->findAll();

        return $this->render('CelcatManagementAppBundle:UserMail:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new UserMail entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new UserMail();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('celcat_management_app_usermail_show', array('id' => $entity->getId())));
        }

        return $this->render('CelcatManagementAppBundle:UserMail:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a UserMail entity.
     *
     * @param UserMail $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(UserMail $entity)
    {
        $form = $this->createForm(new UserMailType(), $entity, array(
            'action' => $this->generateUrl('celcat_management_app_usermail_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new UserMail entity.
     *
     */
    public function newAction()
    {
        $entity = new UserMail();
        $form   = $this->createCreateForm($entity);

        return $this->render('CelcatManagementAppBundle:UserMail:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a UserMail entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:UserMail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserMail entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CelcatManagementAppBundle:UserMail:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing UserMail entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:UserMail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserMail entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CelcatManagementAppBundle:UserMail:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a UserMail entity.
    *
    * @param UserMail $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(UserMail $entity)
    {
        $form = $this->createForm(new UserMailType(), $entity, array(
            'action' => $this->generateUrl('celcat_management_app_usermail_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing UserMail entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:UserMail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserMail entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('celcat_management_app_usermail_show', array('id' => $id)));
        }

        return $this->render('CelcatManagementAppBundle:UserMail:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a UserMail entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CelcatManagementAppBundle:UserMail')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find UserMail entity.');
            }

            $em->remove($entity);
            $em->flush();
            return $this->redirect($this->generateUrl('celcat_management_app_usermail'));
        }
        return $this->render('::Formulaire/supprimer.html.twig', array(
            'form'      => $form->createView(),
            'btnAnnuler'   => $this->generateUrl('celcat_management_app_usermail'),
        ));
    }

    /**
     * Creates a form to delete a UserMail entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('celcat_management_app_usermail_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
