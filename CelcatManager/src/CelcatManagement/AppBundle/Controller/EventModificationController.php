<?php

namespace CelcatManagement\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use CelcatManagement\AppBundle\Entity\EventModification;
use CelcatManagement\AppBundle\Form\EventModificationType;

/**
 * EventModification controller.
 *
 */
class EventModificationController extends Controller
{

    /**
     * Lists all EventModification entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CelcatManagementAppBundle:EventModification')->findAll();

        return $this->render('CelcatManagementAppBundle:EventModification:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new EventModification entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new EventModification();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('celcat_management_app_eventmodification_show', array('id' => $entity->getId())));
        }

        return $this->render('CelcatManagementAppBundle:EventModification:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a EventModification entity.
     *
     * @param EventModification $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(EventModification $entity)
    {
        $form = $this->createForm(new EventModificationType(), $entity, array(
            'action' => $this->generateUrl('celcat_management_app_eventmodification_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new EventModification entity.
     *
     */
    public function newAction()
    {
        $entity = new EventModification();
        $form   = $this->createCreateForm($entity);

        return $this->render('CelcatManagementAppBundle:EventModification:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a EventModification entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:EventModification')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventModification entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CelcatManagementAppBundle:EventModification:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing EventModification entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:EventModification')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventModification entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CelcatManagementAppBundle:EventModification:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a EventModification entity.
    *
    * @param EventModification $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(EventModification $entity)
    {
        $form = $this->createForm(new EventModificationType(), $entity, array(
            'action' => $this->generateUrl('celcat_management_app_eventmodification_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing EventModification entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:EventModification')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find EventModification entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('eventmodification_edit', array('id' => $id)));
        }

        return $this->render('CelcatManagementAppBundle:EventModification:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a EventModification entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CelcatManagementAppBundle:EventModification')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find EventModification entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('celcat_management_app_eventmodification'));
    }

    /**
     * Creates a form to delete a EventModification entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('celcat_management_app_eventmodification_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
