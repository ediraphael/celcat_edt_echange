<?php

namespace CelcatManagement\AppBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use CelcatManagement\AppBundle\Entity\ModelMail;
use CelcatManagement\AppBundle\Form\ModelMailType;

/**
 * ModelMail controller.
 *
 */
class ModelMailController extends Controller
{

    /**
     * Lists all ModelMail entities.
     *
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('CelcatManagementAppBundle:ModelMail')->findAll();

        return $this->render('CelcatManagementAppBundle:ModelMail:index.html.twig', array(
            'entities' => $entities,
        ));
    }
    /**
     * Creates a new ModelMail entity.
     *
     */
    public function createAction(Request $request)
    {
        $entity = new ModelMail();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('modelmail_show', array('id' => $entity->getId())));
        }

        return $this->render('CelcatManagementAppBundle:ModelMail:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Creates a form to create a ModelMail entity.
     *
     * @param ModelMail $entity The entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createCreateForm(ModelMail $entity)
    {
        $form = $this->createForm(new ModelMailType(), $entity, array(
            'action' => $this->generateUrl('modelmail_create'),
            'method' => 'POST',
        ));

        $form->add('submit', 'submit', array('label' => 'Create'));

        return $form;
    }

    /**
     * Displays a form to create a new ModelMail entity.
     *
     */
    public function newAction()
    {
        $entity = new ModelMail();
        $form   = $this->createCreateForm($entity);

        return $this->render('CelcatManagementAppBundle:ModelMail:new.html.twig', array(
            'entity' => $entity,
            'form'   => $form->createView(),
        ));
    }

    /**
     * Finds and displays a ModelMail entity.
     *
     */
    public function showAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:ModelMail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ModelMail entity.');
        }

        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CelcatManagementAppBundle:ModelMail:show.html.twig', array(
            'entity'      => $entity,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing ModelMail entity.
     *
     */
    public function editAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:ModelMail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ModelMail entity.');
        }

        $editForm = $this->createEditForm($entity);
        $deleteForm = $this->createDeleteForm($id);

        return $this->render('CelcatManagementAppBundle:ModelMail:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
    * Creates a form to edit a ModelMail entity.
    *
    * @param ModelMail $entity The entity
    *
    * @return \Symfony\Component\Form\Form The form
    */
    private function createEditForm(ModelMail $entity)
    {
        $form = $this->createForm(new ModelMailType(), $entity, array(
            'action' => $this->generateUrl('modelmail_update', array('id' => $entity->getId())),
            'method' => 'PUT',
        ));

        $form->add('submit', 'submit', array('label' => 'Update'));

        return $form;
    }
    /**
     * Edits an existing ModelMail entity.
     *
     */
    public function updateAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        $entity = $em->getRepository('CelcatManagementAppBundle:ModelMail')->find($id);

        if (!$entity) {
            throw $this->createNotFoundException('Unable to find ModelMail entity.');
        }

        $deleteForm = $this->createDeleteForm($id);
        $editForm = $this->createEditForm($entity);
        $editForm->handleRequest($request);

        if ($editForm->isValid()) {
            $em->flush();

            return $this->redirect($this->generateUrl('modelmail_edit', array('id' => $id)));
        }

        return $this->render('CelcatManagementAppBundle:ModelMail:edit.html.twig', array(
            'entity'      => $entity,
            'edit_form'   => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a ModelMail entity.
     *
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('CelcatManagementAppBundle:ModelMail')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find ModelMail entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('modelmail'));
    }

    /**
     * Creates a form to delete a ModelMail entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('modelmail_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }
}
