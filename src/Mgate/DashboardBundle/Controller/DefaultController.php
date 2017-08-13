<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Mgate\DashboardBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function indexAction()
    {
        return $this->render('MgateDashboardBundle:Default:index.html.twig');
    }

    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        //retrieve search
        $search = $request->query->get('q');

        $projects = $em->getRepository('MgateSuiviBundle:Etude')->searchByNom($search);
        $prospects = $em->getRepository('MgatePersonneBundle:Prospect')->searchByNom($search);
        $people = $em->getRepository('MgatePersonneBundle:Personne')->searchByNom($search);

        return $this->render('MgateDashboardBundle:Default:search.html.twig', [
            'search' => $search,
            'projects' => $projects,
            'prospects' => $prospects,
            'people' => $people,
        ]);
    }
}
