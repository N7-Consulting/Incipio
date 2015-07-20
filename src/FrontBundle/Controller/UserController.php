<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Théo FIDRY <theo.fidry@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FrontBundle\Controller;

//TODO: remove reference to User
use ApiBundle\Entity\User;
use FrontBundle\Form\Type\UserType;
use FrontBundle\Form\UserFilteringForm;
use GuzzleHttp\Query;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController.
 *
 * @Route("/users")
 *
 * @author Théo FIDRY <theo.fidry@gmail.com>
 */
//TODO: refactor this class, has been autogenerated
class UserController extends BaseController
{
    /**
     * Lists all User entities.
     *
     * @Route("/", name="users")
     *
     * @Method({"GET", "POST"})
     * @Template()
     *
     * @param Request $request
     *
     * @return array
     */
    public function indexAction(Request $request)
    {
        $form = $this->createUserFilteringForm($request);
        $userRequest = $this->client->createRequest('GET', 'api_users_cget', $request->getSession()->get('api_token'));

        // Check if a request has been made to filter the list of users
        if ('POST' === $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $data = $form->getData();
                $query = '';
                
                // Update user request to filter the list of users to match the requested type
                if (-1 !== $data['user_type']) {
                    $query .= sprintf('filter[where][type]=%d', $data['user_type']);
                }

                if (0 !== $data['mandate_id']) {
                    $query .= sprintf('filter[where][mandate]=%s', $data['mandate_id']);
                }

                $userRequest->setQuery($query);
            }
        }

        // Retrieve users, since it's a paginated collection go through all available pages
        $users = $this->sendAndDecode($userRequest, true);

        return [
            'users'  => $users,
            'filter' => $form->createView(),
        ];
    }

    /**
     * Creates a new User entity.
     *
     * @Route("/", name="users_create")
     *
     * @Method("POST")
     * @Template()
     *
     * @param Request $request
     *
     * @return array|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function createAction(Request $request)
    {
        $entity = new User();
        $form = $this->createCreateForm($entity);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($entity);
            $em->flush();

            return $this->redirect($this->generateUrl('users_show', array('id' => $entity->getId())));
        }

        return array(
            'entity' => $entity,
            'form' => $form->createView(),
        );
    }

    /**
     * Displays a form to create a new User entity.
     *
     * @Route("/new", name="users_new")
     *
     * @Method("GET")
     * @Template()
     */
    public function newAction()
    {
        $form = $this->createCreateForm();

        return ['form' => $form->createView()];
    }

    /**
     * Finds and displays a User entity.
     *
     * @Route("/{id}", name="users_show")
     *
     * @Method("GET")
     * @Template()
     *
     * @param Request $request
     * @param         $id
     *
     * @return array
     */
    public function showAction(Request $request, $id)
    {
        $response = $this->client->request(
            'GET',
            'users_get',
            $request->getSession()->get('api_token'),
            ['parameters' => ['id' => $id]]
        );

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $user = $this->serializer->decode($response->getBody(), 'json');

        return ['user' => $user];
    }

    /**
     * Displays a form to edit an existing User entity.
     *
     * @Route("/{id}/edit", name="users_edit")
     *
     * @Method("GET")
     * @Template()
     *
     * @param Request $request
     * @param         $id
     *
     * @return array
     */
    public function editAction(Request $request, $id)
    {
        $response = $this->client->request(
            'GET',
            'users_get',
            $request->getSession()->get('api_token'),
            ['parameters' => ['id' => $id]]
        );

        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
            throw $this->createNotFoundException('Unable to find User entity.');
        }

        $user = $this->serializer->decode($response->getBody(), 'json');

        return [
            'user' => $user,
            'form' => $this->createEditForm($user)->createView(),
        ];
    }

    /**
     * Edits an existing User entity.
     *
     * @Route("/{id}", name="users_update")
     *
     * @Method("PUT")
     * @Template("ApiUserBundle:User:edit.html.twig")
     */
    public function updateAction(Request $request, $id)
    {
//        $response = $this->client->get(
//            'users_cget',
//            $request->getSession()->get('api_token')
//        )->send();
//
//        if (Response::HTTP_NOT_FOUND === $response->getStatusCode()) {
//            throw $this->createNotFoundException('Unable to find User entity.');
//        }
//
//        $jsonContent = $response->getBody(true);
//        $user = $this->serializer->decode($jsonContent, 'json');

//        dump($user);
//        die();
//
////        return [
////            'user' => $user,
////            'form' => $this->createEditForm($user)->createView(),
////        ];
//        //TODO
//        $em = $this->getDoctrine()->getManager();
//
//        $entity = $em->getRepository('ApiUserBundle:User')->find($id);
//
//        if (!$entity) {
//            throw $this->createNotFoundException('Unable to find User entity.');
//        }
//
//        $deleteForm = $this->createDeleteForm($id);
//        $editForm = $this->createEditForm($entity);
//        $editForm->handleRequest($request);
//
//        if ($editForm->isValid()) {
//            $em->flush();
//
//            return $this->redirect($this->generateUrl('users_edit', array('id' => $id)));
//        }

        return array(
//            'entity' => $entity,
//            'edit_form' => $editForm->createView(),
//            'delete_form' => $deleteForm->createView(),
        );
    }

    /**
     * Deletes a User entity.
     *
     * @Route("/{id}", name="users_delete")
     *
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $form = $this->createDeleteForm($id);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $entity = $em->getRepository('ApiUserBundle:User')->find($id);

            if (!$entity) {
                throw $this->createNotFoundException('Unable to find User entity.');
            }

            $em->remove($entity);
            $em->flush();
        }

        return $this->redirect($this->generateUrl('users'));
    }

    /**
     * Creates a form to create a User entity.
     *
     * @param array|null $user The normalized user.
     *
     * @return \Symfony\Component\Form\Form
     */
    private function createCreateForm(array $user = [])
    {
        $form = $this->createForm(new UserType(),
            $user,
            [
                'action' => $this->generateUrl('users_create'),
                'method' => 'POST',
            ]
        );

        return $form;
    }

    /**
     * Creates a form to edit a User entity.
     *
     * @param array $user The normalized user.
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createEditForm(array $user)
    {
        $form = $this->createForm(
            new UserType(),
            $user,
            [
                'action' => $this->generateUrl('users_update', ['id' => $user['@id']]),
                'method' => 'PUT',
            ]
        );

        return $form;
    }

    /**
     * Creates a form to delete a User entity by id.
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm($id)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('users_delete', array('id' => $id)))
            ->setMethod('DELETE')
            ->add('submit', 'submit', array('label' => 'Delete'))
            ->getForm()
        ;
    }

    private function createUserFilteringForm(Request $request)
    {
        $mandateFormValues = [];
        $mandates = $this->requestAndDecode(
            'GET',
            'api_mandates_cget',
            $request,
            ['query' => 'filter[order][startAt]=desc'],
            true
        );

        foreach ($mandates as $mandate) {
            $mandateFormValues[$mandate['name']] = $mandate['@id'];
        }

        return $this->createForm(new UserFilteringForm($mandateFormValues),
            [
                'action' => $this->generateUrl('users'),
                'method' => 'POST'
            ])
            ->add('submit', 'submit', ['label' => 'Filtrer'])
        ;
    }
}
