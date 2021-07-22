<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller\Project;

use App\Entity\Project\ClientContact;
use App\Entity\Project\Etude;
use App\Entity\User\User;
use App\Form\Project\EtudeType;
use App\Form\Project\SuiviEtudeType;
use App\Form\Project\AuditEtudeType;
use App\Service\Project\ChartManager;
use App\Service\Project\EtudeManager;
use App\Service\Project\EtudePermissionChecker;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Webmozart\KeyValueStore\Api\KeyValueStore;
use App\Controller\Publish\DocumentController;
use Symfony\Component\HttpKernel\KernelInterface;

class EtudeController extends AbstractController
{
    private $keyValueStore;

    public function __construct(KeyValueStore $keyValueStore)
    {
        $this->keyValueStore = $keyValueStore;
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_etude_homepage", path="/suivi/", methods={"GET","HEAD"})
     *
     * @param string $roleVoirConfidentiel role required to access confidential projects, injected by DI
     *
     * @return Response
     */
    public function index(EtudeManager $etudeManager, string $roleVoirConfidentiel)
    {
        $MANDAT_MAX = $etudeManager->getMaxMandat();
        $MANDAT_MIN = $etudeManager->getMinMandat();

        $em = $this->getDoctrine()->getManager();

        //Etudes En Négociation : stateID = 1
        $etudesEnNegociation = $em->getRepository(Etude::class)
            ->getPipeline(['stateID' => Etude::ETUDE_STATE_NEGOCIATION], ['mandat' => 'DESC', 'num' => 'DESC']);

        //Etudes En Cours : stateID = 2
        $etudesEnCours = $em->getRepository(Etude::class)
            ->getPipeline(['stateID' => Etude::ETUDE_STATE_COURS], ['mandat' => 'DESC', 'num' => 'DESC']);

        //Etudes en pause : stateID = 3
        $etudesEnPause = $em->getRepository(Etude::class)
            ->getPipeline(['stateID' => Etude::ETUDE_STATE_PAUSE], ['mandat' => 'DESC', 'num' => 'DESC']);

        //Etudes finie : stateID = 6
        $etudesFinies = $em->getRepository(Etude::class)
            ->getPipeline(['stateID' => Etude::ETUDE_STATE_FINIE], ['mandat' => 'DESC', 'num' => 'DESC']);

        //Etudes acceptée : stateID = 7
        $etudesAcceptees = $em->getRepository(Etude::class)
            ->getPipeline(['stateID' => Etude::ETUDE_STATE_ACCEPTEE], ['mandat' => 'DESC', 'num' => 'DESC']);

        //Etudes Terminees et Avortees Chargée en Ajax dans getEtudesAsyncAction
        //On push des arrays vides pour avoir les menus déroulants
        $etudesTermineesParMandat = [];
        $etudesAvorteesParMandat = [];

        for ($i = $MANDAT_MIN; $i <= $MANDAT_MAX; ++$i) {
            array_push($etudesTermineesParMandat, []);
            array_push($etudesAvorteesParMandat, []);
        }

        $anneeCreation = $this->keyValueStore->get('anneeCreation');

        return $this->render('Project/Etude/index.html.twig', [
            'etudesEnNegociation' => $etudesEnNegociation,
            'etudesEnCours' => $etudesEnCours,
            'etudesEnPause' => $etudesEnPause,
            'etudesFinies' => $etudesFinies,
            'etudesAcceptees' => $etudesAcceptees,
            'etudesTermineesParMandat' => $etudesTermineesParMandat,
            'etudesAvorteesParMandat' => $etudesAvorteesParMandat,
            'anneeCreation' => $anneeCreation,
            'mandatMax' => $MANDAT_MAX,
            'role_voir_confidentiel' => $roleVoirConfidentiel,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_etude_ajax", path="/suivi/get", methods={"GET","HEAD"})
     *
     * @param string $roleVoirConfidentiel role required to access confidential projects, injected by DI
     *
     * @return Response
     */
    public function getEtudesAsync(Request $request, string $roleVoirConfidentiel)
    {
        $em = $this->getDoctrine()->getManager();

        if ('GET' == $request->getMethod()) {
            $mandat = intval($request->query->get('mandat'));
            $stateID = intval($request->query->get('stateID'));

            if (!empty($mandat) && !empty($stateID)) { // works because state & mandat > 0
                $etudes = $em->getRepository(Etude::class)->findBy(['stateID' => $stateID,
                                                                    'mandat' => $mandat,
                ], ['num' => 'DESC']);

                if (Etude::ETUDE_STATE_CLOTUREE == $stateID) {
                    return $this->render('Project/Etude/Tab/EtudesTerminees.html.twig', [
                        'etudes' => $etudes,
                        'role_voir_confidentiel' => $roleVoirConfidentiel,
                    ]);
                } elseif (Etude::ETUDE_STATE_AVORTEE == $stateID) {
                    return $this->render('Project/Etude/Tab/EtudesAvortees.html.twig', [
                        'etudes' => $etudes,
                        'role_voir_confidentiel' => $roleVoirConfidentiel,
                    ]);
                }
            }
        }

        return $this->render('Project/Etude/Tab/EtudesAvortees.html.twig', [
            'etudes' => null,
            'role_voir_confidentiel' => $roleVoirConfidentiel,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_state", path="/suivi/suivi/state/", methods={"PUT"})
     *
     * @return Response
     */
    public function state(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $stateDescription = !empty($request->request->get('state')) ? $request->request->get('state') : '';
        $stateID = !empty($request->request->get('id')) ? intval($request->request->get('id')) : 0;
        $etudeID = !empty($request->request->get('etude')) ? intval($request->request->get('etude')) : 0;

        if (!$etude = $em->getRepository(Etude::class)->find($etudeID)) {
            throw $this->createNotFoundException('L\'étude n\'existe pas !');
        } else {
            $etude->setStateDescription($stateDescription);
            $etude->setStateID($stateID);
            $em->persist($etude);
            $em->flush();
        }

        return new Response($stateDescription);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_etude_ajouter", path="/suivi/etude/add", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function add(Request $request, EtudeManager $etudeManager)
    {
        $etude = new Etude();

        $etude->setCeActive(true);
        $etude->setCcaActive(false);
        $etude->setKnownProspect(true);
        $etude->setMandat($etudeManager->getMaxMandat());
        $etude->setNum($etudeManager->getNouveauNumero());
        $etude->setFraisDossier($etudeManager->getDefaultFraisDossier());
        $etude->setPourcentageAcompte($etudeManager->getDefaultPourcentageAcompte());

        $user = $this->getUser();
        if (is_object($user) && $user instanceof User) {
            $etude->setSuiveur($user->getPersonne());
        }

        $form = $this->createForm(EtudeType::class, $etude);
        $em = $this->getDoctrine()->getManager();

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if (!$etude->getCcaActive()) {
                    if ((!$etude->isKnownProspect() && !$etude->getNewProspect()) || ($etude->isKnownProspect() && !$etude->getProspect())) {
                        $this->addFlash('danger', 'Vous devez définir un prospect');

                        return $this->render('Project/Etude/ajouter.html.twig', ['form' => $form->createView()]);
                    } elseif (!$etude->isKnownProspect()) {
                        $etude->setProspect($etude->getNewProspect());
                    }
                } else {
                    $prospectCca = $etude->getCca()->getProspect();
                    $etude->setProspect($prospectCca);
                }

                $em->persist($etude);
                $em->flush();
                $this->addFlash('success', 'Etude ajoutée');

                if ($request->get('ap')) {
                    return $this->redirectToRoute('project_ap_rediger', ['id' => $etude->getId()]);
                } else {
                    return $this->redirectToRoute('project_etude_voir', ['nom' => $etude->getNom()]);
                }
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }

        return $this->render(
            'Project/Etude/ajouter.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_etude_voir", path="/suivi/etude/{nom}", methods={"GET","HEAD"})
     *
     * @return Response
     */
    public function voir(Etude $etude, EtudePermissionChecker $permChecker, ChartManager $chartManager)
    {
        $em = $this->getDoctrine()->getManager();

        if ($permChecker->confidentielRefus($etude, $this->getUser())) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        //get contacts clients
        $clientContacts = $em->getRepository(ClientContact::class)->getByEtude($etude, ['date' => 'desc']);

        $ob = $chartManager->getGantt($etude, 'suivi');

        $formSuivi = $this->createForm(SuiviEtudeType::class, $etude);

        return $this->render('Project/Etude/voir.html.twig', [
            'etude' => $etude,
            'formSuivi' => $formSuivi->createView(),
            'chart' => $ob,
            'clientContacts' => $clientContacts,
            /* 'delete_form' => $deleteForm->createView(),  */
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_etude_modifier", path="/suivi/etude/modifier/{nom}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse|Response
     */
    public function modifier(
        Request $request,
        Etude $etude,
        EtudePermissionChecker $permChecker,
        ValidatorInterface $validator
    ) {
        $em = $this->getDoctrine()->getManager();

        if ($permChecker->confidentielRefus($etude, $this->getUser())) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        $form = $this->createForm(EtudeType::class, $etude);

        $deleteForm = $this->createDeleteForm($etude);
        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                if (!$etude->getCcaActive()) {
                    if ((!$etude->isKnownProspect() && !$etude->getNewProspect()) || !$etude->getProspect()) {
                        $this->addFlash('danger', 'Vous devez définir un prospect');

                        return $this->render('Project/Etude/modifier.html.twig', [
                            'form' => $form->createView(),
                            'etude' => $etude,
                            'delete_form' => $deleteForm->createView(),
                        ]);
                    } elseif (!$etude->isKnownProspect()) {
                        $etude->setProspect($etude->getNewProspect());
                    }
                } else {
                    $prospectCca = $etude->getCca()->getProspect();
                    $etude->setProspect($prospectCca);
                }

                $em->persist($etude);
                $em->flush();
                $this->addFlash('success', 'Etude modifiée');

                return $this->redirectToRoute('project_etude_voir', ['nom' => $etude->getNom()]);
            } else {
                $errors = $validator->validate($etude);
                foreach ($errors as $error) {
                    $this->addFlash('danger', $error->getPropertyPath() . ' : ' . $error->getMessage());
                }
            }
        }

        return $this->render('Project/Etude/modifier.html.twig', [
            'form' => $form->createView(),
            'etude' => $etude,
            'delete_form' => $deleteForm->createView(),
        ]);
    }

    /**
     * @Security("has_role('ROLE_ADMIN')")
     * @Route(name="project_etude_supprimer", path="/suivi/etude/supprimer/{nom}", methods={"GET","HEAD","POST"})
     *
     * @return RedirectResponse
     */
    public function delete(Etude $etude, Request $request, EtudePermissionChecker $permChecker,KernelInterface $kernel)
    {
        $form = $this->createDeleteForm($etude);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ($permChecker->confidentielRefus($etude, $this->getUser())) {
                throw new AccessDeniedException('Cette étude est confidentielle');
            }
            $docs = $etude->getRelatedDocuments();
            foreach ($docs as $document) {
                $doc = $document->getDocument();
                DocumentController::deleteRelated($em,$doc,$kernel);
            }
            $em->remove($etude);
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'Etude supprimée');
        }

        return $this->redirectToRoute('project_etude_homepage');
    }

    private function createDeleteForm(Etude $etude)
    {
        return $this->createFormBuilder(['id' => $etude->getId()])
            ->add('id', HiddenType::class)
            ->getForm();
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_etude_suivi", path="/suivi/etudes/suivi", methods={"GET","HEAD"})
     *
     * @return Response
     */
    public function suivi(Request $request, ChartManager $chartManager)
    {
        $em = $this->getDoctrine()->getManager();

        $MANDAT_MAX = 10;

        $etudesParMandat = [];

        for ($i = 1; $i < $MANDAT_MAX; ++$i) {
            array_push(
                $etudesParMandat,
                $em->getRepository(Etude::class)->findBy(['mandat' => $i], ['num' => 'DESC'])
            );
        }

        //WARN
        /* Création d'un form personalisé sans classes (Symfony Forms without Classes)
         *
         * Le problème qui se pose est de savoir si les données reçues sont bien destinées aux bonnes études
         * Si quelqu'un ajoute une étude ou supprime une étude pendant la soumission de se formulaire, c'est la cata
         * tout se décale de 1 étude !!
         * J'ai corrigé ce bug en cas d'ajout d'une étude. Les changements sont bien sauvegardés !!
         * Mais cette page doit être rechargée et elle l'est automatiquement. (Si js est activé !)
         * bref rien de bien fracassant. Solution qui se doit d'être temporaire bien que fonctionnelle !
         * Cependant en cas de suppression d'une étude, chose qui n'arrive pas tous les jours, les données seront perdues !!
         */
        $etudesEnCours = [];

        $NbrEtudes = 0;
        foreach ($etudesParMandat as $etudesInMandat) {
            $NbrEtudes += count($etudesInMandat);
        }

        $form = $this->createFormBuilder();

        if ($this->keyValueStore->exists('namingConvention')) {
            $namingConvention = $this->keyValueStore->get('namingConvention');
        } else {
            $namingConvention = 'id';
        }
        $id = 0;
        foreach (array_reverse($etudesParMandat) as $etudesInMandat) {
            /** @var Etude $etude */
            foreach ($etudesInMandat as $etude) {
                $form = $form->add(
                    (string) (2 * $id),
                    HiddenType::class,
                    ['label' => 'refEtude',
                        'data' => $etude->getReference($namingConvention),
                    ]
                )
                    ->add(
                        (string) (2 * $id + 1),
                        TextareaType::class,
                        ['label' => $etude->getReference($namingConvention),
                            'required' => false, 'data' => $etude->getStateDescription(),
                        ]
                    );
                ++$id;
                if (Etude::ETUDE_STATE_COURS == $etude->getStateID()) {
                    array_push($etudesEnCours, $etude);
                }
            }
        }
        $form = $form->getForm();

        if ('POST' == $request->getMethod()) {
            $form->handleRequest($request);

            $data = $form->getData();

            $id = 0;
            foreach (array_reverse($etudesParMandat) as $etudesInMandat) {
                foreach ($etudesInMandat as $etude) {
                    if ($data[2 * $id] == $etude->getReference($namingConvention)) {
                        if ($data[2 * $id] != $etude->getStateDescription()) {
                            $etude->setStateDescription($data[2 * $id + 1]);
                            $em->persist($etude);
                            ++$id;
                        }
                    } else {
                        echo '<script>location.reload();</script>';
                    }
                }
            }
            $em->flush();
        }

        $ob = $chartManager->getGanttSuivi($etudesEnCours);

        return $this->render('Project/Etude/suiviEtudes.html.twig', [
            'etudesParMandat' => $etudesParMandat,
            'form' => $form->createView(),
            'chart' => $ob,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_etude_suiviQualite", path="/suivi/etudes/suiviQualite", methods={"GET","HEAD"})
     *
     * @return Response
     */
    public function suiviQualite(ChartManager $chartManager)
    {
        $em = $this->getDoctrine()->getManager();

        $etudesEnCours = $em->getRepository(Etude::class)
            ->findBy(['stateID' => Etude::ETUDE_STATE_COURS], ['mandat' => 'DESC', 'num' => 'DESC']);
        $etudesFinies = $em->getRepository(Etude::class)
            ->findBy(['stateID' => Etude::ETUDE_STATE_FINIE], ['mandat' => 'DESC', 'num' => 'DESC']);
        $etudesTerminees = $em->getRepository(Etude::class)
            ->findBy(['stateID' => Etude::ETUDE_STATE_CLOTUREE], ['mandat' => 'DESC', 'num' => 'DESC']);
        $etudes = [$etudesEnCours, $etudesFinies, $etudesTerminees];

        $ob = $chartManager->getGanttSuivi(array_merge($etudesEnCours, $etudesFinies, $etudesTerminees));

        return $this->render('Project/Etude/suiviQualite.html.twig', [
            '_etudes' => $etudes,
            'etudesAudit' => $etudesFinies, // Les études à auditer
            'chart' => $ob,
        ]);
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_etude_suivi_update", path="/suivi/suivi/update/{id}", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function suiviUpdate(Request $request, Etude $etude, EtudePermissionChecker $permChecker)
    {
        $em = $this->getDoctrine()->getManager();

        if ($permChecker->confidentielRefus($etude, $this->getUser())) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        // get previous null documents in order to reset them afterwards
        $ap = null !== $etude->getAp();
        $cc = null !== $etude->getCc();
        $ce = null !== $etude->getCe();
        $cca = null !== $etude->getCca();
        $bdc = null !== $etude->getBdc();

        $formSuivi = $this->createForm(SuiviEtudeType::class, $etude);
        $formSuivi->handleRequest($request);

        if (!$formSuivi->isValid()) {
            $msg = 'Erreurs sur l\'étude: ';
            foreach ($formSuivi->getErrors(true, true) as $error) {
                $msg .= $error->getMessage();
            }

            return new JsonResponse(['responseCode' => Response::HTTP_PRECONDITION_FAILED, 'msg' => $msg,
            ]);
        }

        if (!$ap)
            $etude->setAp();
        if (!$cc)
            $etude->setCc();
        if (!$ce)
            $etude->setCe();
        if (!$cca)
            $etude->setCca();
        if (!$bdc)
            $etude->setBdc();


        $em->persist($etude);
        $em->flush();

        return new JsonResponse(['responseCode' => Response::HTTP_OK, 'msg' => 'ok',
        ]); //make sure it has the correct content type
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="audit_modifier", path="/Project/Etude/Tabvoir/ModifierAudit/{id}", methods={"GET","HEAD","POST"})
     *
     *
     */
    public function auditUpdate(Request $request, Etude $etude, EtudePermissionChecker $permChecker)
    {
        $em = $this->getDoctrine()->getManager();


        if ($permChecker->confidentielRefus($etude, $this->getUser())) {
            throw new AccessDeniedException('Cette étude est confidentielle');
        }

        $formAudit = $this->createForm(AuditEtudeType::class, $etude);
        // $deleteForm = $this->createDeleteFormt($etude->getId());

        if ('POST' == $request->getMethod()) {
            $formAudit->handleRequest($request);

            if ($formAudit->isValid()) {
                $em->persist($etude);
                $em->flush();
                $this->addFlash('success', 'Audit enregistré');
                return $this->redirectToRoute('project_etude_suiviQualite', ['nom' => $etude->getNom()]);
            }
            $this->addFlash('danger', 'Le formulaire contient des erreurs.');
        }


        return $this->render('Project/Etude/TabVoir/ModifierAudit.html.twig',  [
            // 'delete_form' => $deleteForm->createView(),
            'etude' => $etude,
            'formAudit' => $formAudit->createView()]
        );
    }
     /**
     * Function to create a form to remove a formation.
     *
     * @param $id
     *
     * @return \Symfony\Component\Form\FormInterface
     */
    private function createDeleteFormt($id)
    {
        return $this->createFormBuilder(['id' => $id])
            ->add('id', HiddenType::class)
            ->getForm();
    }

    private function getAdjascentKey($key, $hash = array(), $increment)
    {
        $keys = array_keys($hash);
        $found_index = array_search($key, $keys);
        if ($found_index === false) {
            return false;
        }
        $newindex = $found_index + $increment;
        // returns false if no result found
        return ($newindex > 0 && $newindex < sizeof($hash)) ? $keys[$newindex] : false;
    }

    /**
     * @Security("has_role('ROLE_SUIVEUR')")
     * @Route(name="project_vue_ca", path="/suivi/ca/{id}", methods={"GET","HEAD"}, defaults={"id": ""})
     *
     * @param $id
     *
     * @return Response
     */
    public function vuCA($id, ChartManager $chartManager)
    {
        $em = $this->getDoctrine()->getManager();
        $etudes = $em->getRepository(Etude::class)->findBeginningProjects();

        // On trouve soit l'étude associée à l'id
        if ($id > 0) {
            $etude = $em->getRepository(Etude::class)->find($id);
        } else {
            $etude = array_values($etudes)[0];
        }

        if (!$etudes) {
            throw $this->createNotFoundException('Vous devez avoir au moins une étude en négociation, acceptée ou en cours pour accéder à cette page.');
        }


        if (!in_array($etude, $etudes)) {
            throw $this->createNotFoundException('Etude incorrecte');
        }

        /* pagination management */
        $currentKey = array_search($etude, $etudes);
        $nextID = $etudes[$this->getAdjascentKey($currentKey, $etudes, +1)]->getId();
        $prevID = $etudes[$this->getAdjascentKey($currentKey, $etudes, -1)]->getId();

        $ob = $chartManager->getGantt($etude, 'suivi');

        return $this->render('Project/Etude/vuCA.html.twig', [
            'chart' => $ob,
            'nextID' => $nextID,
            'prevID' => $prevID,
            'etude' => $etude,
            'etudes' => $etudes,
        ]);
    }
}
