<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mgate\SuiviBundle\Controller;

use Mgate\SuiviBundle\Entity\ClientContact;
use Mgate\SuiviBundle\Form\Type\ClientContactHandler;
use Mgate\SuiviBundle\Form\Type\ClientContactType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ClientContactController extends Controller
{
    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('MgateSuiviBundle:ClientContact')->findBy([], ['date' => 'ASC']);

        return $this->render('MgateSuiviBundle:ClientContact:index.html.twig', [
            'contactsClient' => $entities,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function addAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$etude = $em->getRepository('Mgate\SuiviBundle\Entity\Etude')->find($id)) {
            throw $this->createNotFoundException('L\'étude n\'existe pas !');
        }

        if ($this->get('Mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        $clientcontact = new ClientContact();
        $clientcontact->setEtude($etude);
        $form = $this->createForm(ClientContactType::class, $clientcontact);
        $formHandler = new ClientContactHandler($form, $request, $em);

        if ($formHandler->process()) {
            return $this->redirect($this->generateUrl('MgateSuivi_clientcontact_voir', ['id' => $clientcontact->getId()]));
        }

        return $this->render('MgateSuiviBundle:ClientContact:ajouter.html.twig', [
            'form' => $form->createView(),
            'etude' => $etude,
        ]);
    }

    private function compareDate(ClientContact $a, ClientContact $b)
    {
        if ($a->getDate() == $b->getDate()) {
            return 0;
        } else {
            return ($a->getDate() < $b->getDate()) ? -1 : 1;
        }
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function voirAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $contactClient = $em->getRepository('MgateSuiviBundle:ClientContact')->find($id);

        if (!$contactClient) {
            throw $this->createNotFoundException('Ce Contact Client n\'existe pas !');
        }

        $etude = $contactClient->getEtude();

        if ($this->get('Mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        $etude = $contactClient->getEtude();
        $contactsClient = $etude->getClientContacts()->toArray();
        usort($contactsClient, [$this, 'compareDate']);

        return $this->render('MgateSuiviBundle:ClientContact:voir.html.twig', [
            'contactsClient' => $contactsClient,
            'selectedContactClient' => $contactClient,
            'etude' => $etude,
            ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     */
    public function modifierAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$clientcontact = $em->getRepository('Mgate\SuiviBundle\Entity\ClientContact')->find($id)) {
            throw $this->createNotFoundException('Ce Contact Client n\'existe pas !');
        }

        $etude = $clientcontact->getEtude();

        if ($this->get('Mgate.etude_manager')->confidentielRefus($etude, $this->getUser(), $this->get('security.authorization_checker'))) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        $form = $this->createForm(ClientContactType::class, $clientcontact);

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);

            if ($form->isValid()) {
                $em->flush();

                return $this->redirect($this->generateUrl('MgateSuivi_clientcontact_voir', ['id' => $clientcontact->getId()]));
            }
        }

        return $this->render('MgateSuiviBundle:ClientContact:modifier.html.twig', [
            'form' => $form->createView(),
            'clientcontact' => $clientcontact,
            'etude' => $etude,
        ]);
    }
}
