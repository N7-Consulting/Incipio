<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\PersonneBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use JMS\SecurityExtraBundle\Annotation\Secure;
use mgate\PersonneBundle\Entity\Membre;
use mgate\PersonneBundle\Entity\Personne;

class PersonneController extends Controller
{
    /**
     * @Secure(roles="ROLE_CA")
     */
    public function annuaireAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Personne')->findAll();

        return $this->render('mgatePersonneBundle:Personne:annuaire.html.twig', array(
            'personnes' => $entities,
        ));
    }

    /**
     * @Secure(roles="ROLE_CA")
     */
    public function listeMailAction()
    {
        $em = $this->getDoctrine()->getManager();

        $entities = $em->getRepository('mgatePersonneBundle:Personne')->getAllPersonne();

        //$membres = $em->getRepository('mgatePersonneBundle:Membre')->getCotisants();

        $cotisants = array();
        $cotisantsEtu = array();
        /** foreach ($membres as $cotisant) {
         * $nom = $cotisant->getPersonne()->getNom().' '.$cotisant->getPersonne()->getPrenom();
         *
         * $mailEtu = $cotisant->getEmailEMSE();
         * $mail = $cotisant->getPersonne()->getEmail();
         * if ($mail !== null) {
         * $cotisants[$nom] = $mail;
         * }
         * if ($mailEtu !== null) {
         * $cotisantsEtu[$nom] = $mailEtu;
         * }
         * }
         * ksort($cotisants);
         * ksort($cotisantsEtu);
         * **/

        $nbrCotisants = count($cotisants);
        $nbrCotisantsEtu = count($cotisantsEtu);

        $listCotisants = '';
        $listCotisantsEtu = '';
        foreach ($cotisants as $nom => $mail) {
            $listCotisants .= "$nom <$mail>; ";
        }
        foreach ($cotisantsEtu as $nom => $mail) {
            $listCotisantsEtu .= "$nom <$mail>; ";
        }

        return $this->render('mgatePersonneBundle:Personne:listeDiffusion.html.twig', array(
            'personnes' => $entities,
            'cotisants' => $listCotisants,
            'cotisantsEtu' => $listCotisantsEtu,
            'nbrCotisants' => $nbrCotisants,
            'nbrCotisantsEtu' => $nbrCotisantsEtu,

        ));
    }

    /**
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        if (!$entity = $em->getRepository('mgate\PersonneBundle\Entity\Personne')->find($id)) {
            throw $this->createNotFoundException('La personne demandée n\'existe pas !');
        }

        $em->remove($entity);
        $em->flush();

        return $this->redirect($this->generateUrl('mgatePersonne_annuaire'));
    }
}
