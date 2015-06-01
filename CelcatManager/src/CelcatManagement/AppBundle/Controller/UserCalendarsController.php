<?php

namespace CelcatManagement\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use CelcatManagement\AppBundle\Entity\UserCalendars;
use CelcatManagement\AppBundle\Form\UserCalendarsType;

/**
 * UserCalendars controller.
 *
 */
class UserCalendarsController extends Controller
{

    /**
     * Lists all UserCalendars entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CelcatManagementAppBundle:UserCalendars')->findAll();

        return $this->render('CelcatManagementAppBundle:UserCalendars:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new UserCalendars entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new UserCalendars();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('usercalendars_show', array('id' => $entity->getId())));
        }

        return $this->render('CelcatManagementAppBundle:UserCalendars:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a UserCalendars entity.
     *
     * @param UserCalendars $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(UserCalendars $entity)
    {
        $form = $this->createForm(new UserCalendarsType(), $entity, array(
            'action' => $this->generateUrl('usercalendars_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new UserCalendars entity.
     *
     */
    public function newAction()
    {
        $entity = new UserCalendars();
        $form   = $this->createCreateForm($entity);

        return $this->render('CelcatManagementAppBundle:UserCalendars:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a UserCalendars entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:UserCalendars')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserCalendars entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CelcatManagementAppBundle:UserCalendars:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing UserCalendars entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:UserCalendars')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserCalendars entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CelcatManagementAppBundle:UserCalendars:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a UserCalendars entity.
    *
    * @param UserCalendars $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(UserCalendars $entity)
    {
        $form = $this->createForm(new UserCalendarsType(), $entity, array(
            'action' => $this->generateUrl('usercalendars_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing UserCalendars entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:UserCalendars')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find UserCalendars entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('usercalendars_edit', array('id' => $id)));
        }

        return $this->render('CelcatManagementAppBundle:UserCalendars:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a UserCalendars entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CelcatManagementAppBundle:UserCalendars')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find UserCalendars entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('usercalendars'));
    }

    /**
     * Creates a form to delete a UserCalendars entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('usercalendars_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
