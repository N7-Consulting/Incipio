<?php

namespace App\Command;

use App\Entity\Formation\Passation;
use App\Entity\Personne\SecteurActivite;
use App\Entity\Formation\Formation;
use App\Entity\Processus\Processus;
use App\Entity\Hr\Competence;
use App\Entity\Hr\Alumnus;
use App\Entity\Personne\Employe;
use App\Entity\Personne\Filiere;
use App\Entity\Personne\Mandat;
use App\Entity\Personne\Membre;
use App\Entity\Personne\Personne;
use App\Entity\Personne\Poste;
use App\Entity\Personne\Prospect;
use App\Entity\Project\Bdc;
use App\Entity\Project\Ce;
use App\Entity\Project\Cca;
use App\Entity\Project\Etude;
use App\Entity\Project\GroupePhases;
use App\Entity\Project\Mission;
use App\Entity\Project\Phase;
use App\Entity\Project\ProcesVerbal;
use App\Entity\Treso\Compte;
use App\Entity\Treso\Facture;
use App\Entity\Treso\FactureDetail;
use App\Service\Project\EtudeManager;
use DateInterval;
use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CreateDemoDataCommand extends Command
{
    const NOM = ['Henry', 'Martinez', 'Durand', 'Duval', 'Leroux', 'Robert', 'Morel', 'Bourgeois', 'Dupont', 'Dumont', 'Bernard', 'Francois', 'Dupond', 'Dubois', 'Blanc', 'Paul', 'Petit'];

    const PRENOM = ['Alexandre', 'Paul', 'Thomas', 'Raphaël', 'Camille', 'Inès', 'Emma', 'Gabriel', 'Antoine', 'Louis', 'Victor', 'Maxime', 'Hugo', 'Louise', 'Marie', 'Sarah', 'Arthur', 'Clara', 'Lea', 'Alice', 'Lucas', 'Jules', 'Chloe', 'Elsa', 'Manon'];

    const FILIERES = ['Hydro', 'Electronique', 'Telecoms', 'Automatique', 'Info'];

    const ALUMNI = [
        [
            'commentaire' => 'L\'alumnus est interessé par notre proposition de parrainage. ',
            'lienLinkedIn' => 'https://fr.linkedin.com/',
            'secteurActuel' => 'Informatique',
            'posteActuel' => 'Chef de projet'
        ]
    ];

    const FORMATIONS = [
        [
            'titre' => 'Présentaion orale',
            'description' => 'Comment créer et présenter un diaporama.',
            'categorie' => 1,
        ],
        [
            'titre' => 'Formation Jeyser',
            'description' => 'Comment utiliser correctement Jeyser et utiliser toutes ses fonctionnalités.',
            'categorie' => 7,
        ]
    ];

    const PROCESSUS = [
        [
            'nom' => 'R.F.P',
            'description' => 'Fiches de processus liées au R.F.P',

        ],
        [
            'nom' => 'Sécurité informatique',
            'description' => 'Procédures à suivre lors d\'une attaque informatique',
        ]
    ];

    const PASSATIONS = [
        [
            'titre' => 'Plan d\'action',
            'description' => 'Comment réaliser et suivre un plan d\'action et le suivre durant le mandat.',
            'categorie' => 1,
        ],
        [
            'titre' => 'Identifiants et mot de passe',
            'description' => 'Document contenant les identifiants et les mots de passe utilisés par le pôle.',
            'categorie' => 4,
        ]
    ];

    // L'ordre importe! cf ETUDES.
    const PROSPECT = [
        [
            'entreprise' => 'Gladiator Consulting',
            'adresse' => '3 rue du chene noir',
            'codePostal' => 33100,
            'ville' => 'Toulouse',
            'entite' => 2,
            'email' => 'contact@glad.fr',
        ],
        [
            'entreprise' => 'Blackwater',
            'adresse' => '1020 5th Avenue',
            'codePostal' => 92200,
            'ville' => 'Neuilly',
            'entite' => 3,
            'email' => 'hello@black.ninja',
        ],
        [
            'entreprise' => 'Imuka',
            'adresse' => 'Kuruma San',
            'codePostal' => 91000,
            'ville' => 'Evry',
            'entite' => 4,
            'email' => 'contact@imuka.jp',
        ],
        [
            'entreprise' => 'Universal rad',
            'adresse' => '2 rue Marie Curie',
            'codePostal' => 35000,
            'ville' => 'Rennes',
            'entite' => 5,
            'email' => 'contact@univ.radar',
        ],
        [
            'entreprise' => 'Teknik studio',
            'adresse' => '10 impasse sunderland',
            'codePostal' => 35000,
            'ville' => 'Rennes',
            'entite' => 6,
            'email' => 'contact@teknik.paris',
        ],
        [
            'entreprise' => 'Duvilcolor',
            'adresse' => '600 la pyrennene',
            'codePostal' => 33100,
            'ville' => 'Labege',
            'entite' => 4,
            'email' => 'contact@duvilcol.or',
        ],
        [
            'entreprise' => 'Nilsen Industries',
            'adresse' => '2 rue saint-louis',
            'codePostal' => 31000,
            'ville' => 'Bordeaux',
            'entite' => 7,
            'email' => 'contact@nislen.com',
        ],
        [
            'entreprise' => 'PRR',
            'adresse' => 'PRR',
            'codePostal' => 35000,
            'ville' => 'Rennes',
            'entite' => 4,
            'email' => 'contact@prr.cn',
        ],
    ];

    CONST CCA = [
        [
            'nom' => '604GLA',
            'prospect' => 'Gladiator Consulting',
            'dateSignature' => '-70 days',
            'dateFin' => '+300 days',
        ],
        [
            'nom' => '605BLA',
            'prospect' => 'Blackwater',
            'dateSignature' => '-50 days',
            'dateFin' => '+20 days',
        ],
        [
            'nom' => '581IMU',
            'prospect' => 'Imuka',
            'dateSignature' => '-200 days',
            'dateFin' => '-25 days',
        ],
    ];

    const ETUDES = [
        [
            'nom' => '604GLA-BdC1',
            'description' => 'Realisation site web statique',
            'statut' => Etude::ETUDE_STATE_FINIE,
            'nbrJEH' => 9,
            'duree' => 5,
            'cca' => '604GLA',
            'prospect' => 'Gladiator Consulting',
        ],
        [
            'nom' => '604GLA-BdC2',
            'description' => 'Implantation partie backend au site',
            'statut' => Etude::ETUDE_STATE_ACCEPTEE,
            'nbrJEH' => 17,
            'duree' => 10,
            'cca' => '604GLA',
            'prospect' => 'Gladiator Consulting',
        ],
        [
            'nom' => '605BLA-BdC1',
            'description' => 'Electronique avancee',
            'statut' => Etude::ETUDE_STATE_COURS,
            'nbrJEH' => 5,
            'duree' => 3,
            'cca' => '605BLA',
            'prospect' => 'Blackwater',
        ],
        [
            'nom' => '581IMU-BdC1',
            'description' => 'Design Base de donnes',
            'statut' => Etude::ETUDE_STATE_CLOTUREE,
            'nbrJEH' => 8,
            'duree' => 4,
            'cca' => '581IMU',
            'prospect' => 'Imuka',
        ],
        [
            'nom' => '602UNI',
            'description' => 'Conception Radar recul',
            'statut' => Etude::ETUDE_STATE_FINIE,
            'nbrJEH' => 12,
            'duree' => 8,
            'prospect' => 'Universal rad',
        ],
        [
            'nom' => '615TEK',
            'description' => 'Refactorisation code Java',
            'statut' => Etude::ETUDE_STATE_COURS,
            'nbrJEH' => 10,
            'duree' => 8,
            'prospect' => 'Teknik studio',
        ],
        [
            'nom' => '616DUV',
            'description' => 'Calcul de flux thermique',
            'statut' => Etude::ETUDE_STATE_NEGOCIATION,
            'nbrJEH' => 9,
            'duree' => 4,
            'prospect' => 'Duvilcolor',
        ],
        [
            'nom' => '618NIL',
            'description' => 'Application Android',
            'statut' => Etude::ETUDE_STATE_NEGOCIATION,
            'nbrJEH' => 8,
            'duree' => 12,
            'prospect' => 'Nilsen Industries',
        ],
        [
            'nom' => '622PRR',
            'description' => 'Etude de faisabilite',
            'statut' => Etude::ETUDE_STATE_PAUSE,
            'nbrJEH' => 4,
            'duree' => 4,
            'prospect' => 'PRR',
        ],
    ];

    /** @var ObjectManager */
    private $em;

    /** @var ValidatorInterface */
    private $validator;

    /** @var EtudeManager */
    private $etudeManager;

    /** @var Membre[] */
    private $membres = [];

    /** @var Prospect[] */
    private $prospects = [];

    /** @var Cca[] */
    private $ccas = [];

    /** @var Etude[] */
    private $etudes = [];

    /** @var Membre */
    private $president;

    /** @var Membre */
    private $vp;

    /** @var Filiere[] */
    private $filieres = [];

    /** @var Competence[] */
    private $competences = [];

    public function __construct(ObjectManager $em, ValidatorInterface $validator, EtudeManager $etudeManager)
    {
        parent::__construct();
        $this->em = $em;
        $this->validator = $validator;
        $this->etudeManager = $etudeManager;
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('demo:create_data')
            ->setDescription('Create some demonstration data')
            ->setHelp('Creates some fake data for every module in order to have a nice overview of all functionnality.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->competences = $this->em->getRepository(Competence::class)->findAll();

        $this->createFilieres($output);
        $this->createMembres($output);

        $this->createProspects($output);
        $this->createCcas($output);
        $this->createEtudes($output);

        // Manage CE, BDC, FA, RM, PV and their states
        $this->createDocuments($output);

        $this->createFormation($output);
        $this->createPassation($output);
        $this->createProcessus($output);
        $this->createAlumni($output);

        $output->writeln('Done.');
    }

    private function createFilieres(OutputInterface $output)
    {
        /* Filiere management */
        foreach (self::FILIERES as $ff) {
            $nf = new Filiere();
            $nf->setDescription('Demo filiere');
            $nf->setNom($ff);
            $this->validateObject('New filiere', $nf);
            $this->em->persist($nf);
            $this->filieres[] = $nf;
        }
        $this->em->flush();
        $output->writeln('Filiere: Ok');
    }

    private function createMembres(OutputInterface $output)
    {
        $mandat = date('Y') + 2;

        $vp = $this->createMembre(self::PRENOM[array_rand(self::PRENOM)], self::NOM[array_rand(self::NOM)], $mandat);
        $initial = substr($vp->getPersonne()->getPrenom(), 0, 1) . substr($vp->getPersonne()->getNom(), 0, 1);
        $vp->setIdentifiant(strtoupper($initial . '1'));
        $m1 = new Mandat();
        $m1->setDebutMandat(new \DateTime(($mandat - 2) . '-03-16'));
        $m1->setFinMandat(new \DateTime(($mandat - 1) . '-03-16'));
        $m1->setMembre($vp);
        $m1->setPoste($this->em->getRepository(Poste::class)->findOneByIntitule('Vice-président'));
        $this->validateObject('Mandat VP', $m1);
        $this->em->persist($m1);
        $this->em->persist($vp);
        $this->vp = $vp;

        $president = $this->createMembre(self::PRENOM[array_rand(self::PRENOM)], self::NOM[array_rand(self::NOM)], $mandat);
        $initial = substr($president->getPersonne()->getPrenom(), 0, 1) . substr($president->getPersonne()->getNom(), 0, 1);
        $president->setIdentifiant(strtoupper($initial . '2'));
        $m2 = new Mandat();
        $m2->setDebutMandat(new \DateTime(($mandat - 2) . '-03-16'));
        $m2->setFinMandat(new \DateTime(($mandat - 1) . '-03-16'));
        $m2->setMembre($president);
        $m2->setPoste($this->em->getRepository(Poste::class)->findOneByIntitule('Président'));
        $this->em->persist($president);
        $this->em->persist($m2);
        $this->president = $president;
        $this->em->flush();

        $output->writeln('President & VP: Ok');
    }

    private function createProspects(OutputInterface $output)
    {
        foreach (self::PROSPECT as $prospect) {
            $p = new Prospect();
            $p->setNom($prospect['entreprise']);
            $p->setAdresse($prospect['adresse']);
            $p->setCodePostal($prospect['codePostal']);
            $p->setVille($prospect['ville']);
            $p->setEntite($prospect['entite']);

            $pe = new Personne();
            $pe->setPrenom(self::PRENOM[array_rand(self::PRENOM)]); //whitespace explode : not perfect but better than nothing
            $pe->setNom(self::NOM[array_rand(self::NOM)]);
            $pe->setEmailEstValide(true);
            $pe->setEstAbonneNewsletter(false);
            $pe->setEmail($prospect['email']);
            $pe->setAdresse($prospect['adresse']);
            $pe->setCodePostal($prospect['codePostal']);
            $pe->setVille($prospect['ville']);

            $emp = new Employe();
            $emp->setProspect($p);
            $p->addEmploye($emp);
            $emp->setPersonne($pe);
            $this->validateObject('New Prospect', $p);
            $this->validateObject('New Employe', $emp);
            $this->em->persist($emp->getPersonne());
            $this->em->persist($emp);
            $this->em->persist($p);


            $this->prospects[$prospect['entreprise']] = $p;
        }

        $this->em->flush();
        $output->writeln('Prospects: Ok');
    }

    private function createCcas(OutputInterface $output)
    {
        foreach (self::CCA as $cca) {
            $dateSignature = new DateTime();
            $dateFin = new DateTime();

            $c = new Cca();
            $c->setNom($cca['nom']);
            $c->setProspect($this->prospects[$cca['prospect']]);
            $c->setDateSignature($dateSignature->modify($cca['dateSignature']));
            $c->setDateFin($dateFin->modify($cca['dateFin']));

            $this->validateObject('New Cca', $c);
            $this->em->persist($c);
            $this->ccas[$cca['prospect']] = $c;
        }

        $this->em->flush();
        $output->writeln('Ccas: Ok');
    }

    private function createEtudes(OutputInterface $output)
    {
        foreach (self::ETUDES as $etude) {
            $e = new Etude();
            $mandatDefault = $this->etudeManager->getMaxMandat();
            // hack with 581IMU, to have some decent stats on welcome page
            $mandat = ('581IMU-BdC1' === $etude['nom'] ? $mandatDefault - 1 : $mandatDefault);
            $randomDate = new DateTime(date('Y') . '-' . rand(1,10) . '-' . rand(1, 30));
            if ($etude['nom'] === '581IMU-BdC1')
                $randomDate = (new DateTime())->modify('-365 days');
            $e->setMandat($mandat);
            $e->setNom($etude['nom']);
            $e->setDescription($etude['description']);
            $e->setDateCreation($randomDate);
            $e->setStateID($etude['statut']);
            $e->setAcompte(true);
            $e->setCeActive(true); // Valeur par défaut depuis Jeyser 4.0.0, les AP et CC ne sont plus utilisées.
            if(array_key_exists('cca', $etude)) {
                $e->setCcaActive(true);
                $e->setCca($this->ccas[$etude['prospect']]);
            }
            $e->setPourcentageAcompte(0.3);
            $e->setFraisDossier(90);
            $e->setPresentationProjet('Presentation ' . $etude['description']);
            $e->setDescriptionPrestation('Describe what we will do here');
            $e->setSourceDeProspection(rand(1, 10));
            $e->setProspect($this->prospects[$etude['prospect']]);
            $e->setPC("3");
            $this->validateObject('New Etude', $e);
            $this->em->persist($e);
            $c = $this->competences[array_rand($this->competences)];
            if (null !== $c) {
                $c->addEtude($e);
            }

            //create phases
            $g = new GroupePhases(); //default group
            $g->setTitre('Random generated' . rand());
            $g->setNumero(1);
            $g->setDescription('Automatic description');
            $g->setEtude($e);
            $this->validateObject('New GroupePhases', $g);
            $this->em->persist($g);

            $k = rand(1, 3);
            for ($i = 0; $i < $k; ++$i) {
                $ph = new Phase();
                $ph->setEtude($e);
                $ph->setGroupe($g);
                $ph->setPosition($i);
                $ph->setNbrJEH(intval($etude['nbrJEH'] / $k));
                $ph->setPrixJEH(340);
                $ph->setTitre('phase ' . $i);
                $ph->setDelai(intval(($etude['duree'] * 7) / $k) - $i);
                $ph->setDateDebut($randomDate);
                $this->validateObject('New Phase ' . $i, $ph);
                $this->em->persist($ph);
            }

            //manage project manager
            $pm = $this->createMembre(self::PRENOM[array_rand(self::PRENOM)], self::NOM[array_rand(self::NOM)], $mandat + 2);
            $this->em->persist($pm);
            if (null !== $c && !$c->getMembres()->contains($pm)) {
                $c->addMembre($pm);
            }
            $e->setSuiveur($pm->getPersonne());
            $e->setSuiveurQualite($this->membres[array_rand($this->membres)]->getPersonne());

            //manage intervenant
            if ($etude['statut'] !== Etude::ETUDE_STATE_NEGOCIATION &&  $etude['statut'] !== Etude::ETUDE_STATE_ACCEPTEE && $etude['statut'] !== Etude::ETUDE_STATE_AVORTEE) {
                $k = rand(1, 4);
                for ($i = 0; $i < $k; ++$i) {
                    $mdev = $this->createMembre(self::PRENOM[array_rand(self::PRENOM)], self::NOM[array_rand(self::NOM)], $mandat + 1);
                    $this->em->persist($mdev);
                    if (null !== $c && !$c->getMembres()->contains($mdev)) {
                        $c->addMembre($mdev);
                    }

                    $mi = new Mission();
                    $mi->setSignataire2($mdev->getPersonne());
                    $mi->setSignataire1($this->president->getPersonne());
                    $mi->setEtude($e);
                    $mi->setDateSignature($randomDate);
                    $mi->setDebutOm($randomDate);
                    $mi->setFinOm((new DateTime($randomDate->format('Y-m-d')))->modify('+30 days'));
                    $mi->setAvancement(rand(10, 95));
                    $mi->setIntervenant($mdev);
                    $this->validateObject('New Mission', $mi);
                    $e->addMission($mi);
                    $this->em->persist($mi);
                }
            }

            $this->etudes[$etude['nom']] = $e;
        }

        $this->em->flush();
        $output->writeln('Etudes: Ok');
    }

    private function createDocuments(OutputInterface $output)
    {
        /** @var Etude $etude */
        foreach ($this->etudes as $k => $etude) {
            $etatDoc = 0; // Entre 0 et 4, pour décider de l'état d'un document (rédigé, relu, etc). Si > 4, état max.
            switch ($etude->getStateID()) {
                case ETUDE::ETUDE_STATE_CLOTUREE:
                    $etatDoc = 4;
                case Etude::ETUDE_STATE_FINIE:
                    $etatDoc = $etatDoc == 0 ? rand(2,4) : $etatDoc;
                    // ! Procès verbal
                    $this->createProcesVerbal($etude, $etatDoc);
                case Etude::ETUDE_STATE_COURS:
                    $etatDoc = $etatDoc == 0 ? rand(1,4) : $etatDoc + 10;
                case Etude::ETUDE_STATE_ACCEPTEE:
                    $etatDoc = $etatDoc == 0 ? rand(0,3) : $etatDoc;
                    // ! Facture d'acompte
                    $this->createFactureAcompte($etude);
                    // ! Missions
                    $this->setEtatRMs($etude, $etatDoc);
                case Etude::ETUDE_STATE_NEGOCIATION:
                    $etatDoc = $etatDoc == 0 ? rand(0,4) : $etatDoc + 10;
                case Etude::ETUDE_STATE_PAUSE:
                case Etude::ETUDE_STATE_AVORTEE:
                    // ! BdC ou CE
                    $this->createBonCommande($etude, $etatDoc);
                    $this->createConventionEtude($etude, $etatDoc);
                default:
                    break;
            }
        }
        $this->em->flush();
        $output->writeln('Documents: Ok');
    }

    private function etatDoc($doc, $etatDoc) {
        switch ($etatDoc) {
            default:
            case 4:
                $doc->setReceptionne(true);
            case 3:
                $doc->setEnvoye(true);
            case 2:
                $doc->setRelu(true);
            case 1:
                $doc->setRedige(true);
            case 0:
                break;
        }
    }

    private function setEtatRMs($etude, $etatDoc)
    {
        foreach ($etude->getMissions() as $k => $mission) {
            $this->etatDoc($mission, $etatDoc);
        }
    }

    /** Used within createDocuments */
    private function createConventionEtude(Etude $etude, $etatDoc) {
        if (!$etude->getCcaActive()) {
            $emp = $etude->getProspect()->getEmployes()[0];

            $ce = new Ce();
            $ce->setDateSignature($etude->getDateCreation());
            $ce->setSignataire1($this->president->getPersonne());
            $ce->setSignataire2(null !== $emp ? $emp->getPersonne() : null);

            // Set etat document
            $this->etatDoc($ce, $etatDoc);

            $etude->setCe($ce);

            $this->validateObject('New Ce', $ce);
            $this->em->persist($ce);
        }
    }

    /** Used within createDocuments */
    private function createBonCommande(Etude $etude, $etatDoc) {
        if ($etude->getCcaActive()) {
            $emp = $etude->getProspect()->getEmployes()[0];

            $bdc = new Bdc();
            $bdc->setDateSignature($etude->getDateCreation());
            $bdc->setSignataire1($this->president->getPersonne());
            $bdc->setSignataire2(null !== $emp ? $emp->getPersonne() : null);

            // Set etat document
            $this->etatDoc($bdc, $etatDoc);

            $etude->setBdc($bdc);

            $this->validateObject('New Bdc', $bdc);
            $this->em->persist($bdc);
        }
    }

    /** Used within createDocuments */
    private function createProcesVerbal(Etude $etude, $etatDoc)
    {
        $emp = $etude->getProspect()->getEmployes()[0];

        $pv = new ProcesVerbal();
        $pv->setEtude($etude);
        $endDate = clone $etude->getDateCreation();
        $pv->setDateSignature($endDate->modify('+1 month'));
        $pv->setSignataire1($this->president->getPersonne());
        $pv->setSignataire2(null !== $emp ? $emp->getPersonne() : null);

        // Set etat document
        $this->etatDoc($pv, $etatDoc);

        $etude->setPvr($pv);

        $this->validateObject('New PVRF', $pv);
        $this->em->persist($pv);
    }

    /** Used within createDocuments */
    private function createFactureAcompte(Etude $etude)
    {
        $compteAcompte = 419100;

        $fa = new Facture();
        $fa->setType(Facture::TYPE_VENTE_ACCOMPTE);
        $fa->setObjet('Facture d\'acompte sur l\'étude ' . $etude->getReference('nom') . ', correspondant au règlement de ' . (($etude->getPourcentageAcompte() * 100)) . ' % de l’étude.');
        $fa->setExercice($etude->getDateCreation()->format('Y'));
        $fa->setNumero(1);
        $fa->setEtude($etude);
        $fa->setBeneficiaire($etude->getProspect());
        $endDate = clone $etude->getDateCreation();
        $fa->setDateEmission($endDate->modify('+1 month'));

        $detail = new FactureDetail();
        $detail->setCompte($this->em->getRepository(Compte::class)->findOneBy(['numero' => $compteAcompte]));
        $detail->setFacture($fa);
        $fa->addDetail($detail);
        $detail->setDescription('Acompte de ' . ($etude->getPourcentageAcompte() * 100) . ' % sur l\'étude ' . $etude->getReference());
        $detail->setMontantHT($etude->getPourcentageAcompte() * $etude->getMontantHT());
        $detail->setTauxTVA(20);

        $this->validateObject('New FA', $fa);
        $this->em->persist($fa);
    }

    /**
     * @param $prenom
     * @param $nom
     * @param $promotion
     *
     * @return Membre
     */
    private function createMembre($prenom, $nom, $promotion)
    {
        $vp = new Personne();
        $vp->setPrenom($prenom);
        $vp->setNom($nom);
        $vp->setEmail($prenom . '' . $nom . '@localhost.localdomain');
        $vp->setMobile('0' . rand(111111111, 999999999));
        $vp->setEmailEstValide(false);
        $vp->setEstAbonneNewsletter(false);
        $this->validateObject('New vp Personne', $vp);
        $this->em->persist($vp);

        $mvp = new Membre();
        $mvp->setPersonne($vp);
        $mvp->setPromotion($promotion);
        $mvp->setEmailEMSE(substr($prenom, 0, 1) . '' . $nom . '@etu.localdomain');
        $mvp->setFiliere($this->filieres[array_rand($this->filieres)]);
        $this->validateObject('New vp Membre', $mvp);
        $this->em->persist($mvp);
        $c = $this->competences[array_rand($this->competences)];
        $c->addMembre($mvp);
        $this->membres[] = $mvp;

        return $mvp;
    }


    private function createFormation(OutputInterface $output)
    {
        foreach (self::FORMATIONS as $formation) {
            $fo = new Formation();

            $date = new \DateTime('now');
            $year = $date->format('Y');
            $month = rand(1, 10);
            $day = rand(1, 27);

            $fo->setTitre($formation['titre']);
            $fo->setDescription($formation['description']);

            $fo->setMandat($year);
            $fo->setCategorie($formation['categorie']);

            $fo->setDateDebut(new \DateTime($year . '-' . $month . '-' . $day . ' 8:0:0'));
            $fo->setDateFin(new \DateTime($year . '-' . $month . '-' . $day . ' 9:0:0'));

            $pe = new Personne();
            $prenom = self::PRENOM[array_rand(self::PRENOM)];
            $nom = self::NOM[array_rand(self::NOM)];
            $pe->setPrenom($prenom);
            $pe->setNom($nom);
            $pe->setEmail($prenom . '' . $nom . '@localhost.localdomain');
            $pe->setMobile('0' . rand(111111111, 999999999));
            $pe->setEmailEstValide(false);
            $pe->setEstAbonneNewsletter(false);
            $this->validateObject('New Personne', $pe);
            $this->em->persist($pe);
            $formateur[] = $pe;

            $pep = new Personne();
            $prenom = self::PRENOM[array_rand(self::PRENOM)];
            $nom = self::NOM[array_rand(self::NOM)];
            $pep->setPrenom($prenom);
            $pep->setNom($nom);
            $pep->setEmail($prenom . '' . $nom . '@localhost.localdomain');
            $pep->setMobile('0' . rand(111111111, 999999999));
            $pep->setEmailEstValide(false);
            $pep->setEstAbonneNewsletter(false);
            $this->validateObject('New présent', $pep);
            $this->em->persist($pep);
            $membre[] = $pep;

            $fo->setFormateurs($formateur);
            $fo->setMembresPresents($membre);

            $this->validateObject('New Formation', $fo);
            $this->em->persist($fo);

            $this->em->flush();
        }
        $output->writeln('Formations: Ok');
    }


    private function createPassation(OutputInterface $output)
    {
        foreach (self::PASSATIONS as $passation) {
            $pas = new Passation();

            $pas->setTitre($passation['titre']);
            $pas->setDescription($passation['description']);
            $pas->setCategorie($passation['categorie']);

            $this->validateObject('New Passation', $pas);
            $this->em->persist($pas);

            $this->em->flush();
        }
        $output->writeln('Passations: Ok');
    }

    private function createAlumni(OutputInterface $output)
    {
        foreach (self::ALUMNI as $alumnus) {
            $al = new Alumnus();

            $al->setCommentaire($alumnus['commentaire']);
            $al->setLienLinkedIn($alumnus['lienLinkedIn']);
            $al->setPosteActuel($alumnus['posteActuel']);

            $date = new \DateTime('now');
            $promotion = $date->format('Y') + 3;

            $pe = $this->createMembre(self::PRENOM[array_rand(self::PRENOM)], self::NOM[array_rand(self::NOM)], $promotion);
            $al->setPersonne($pe);

            $secteur = new SecteurActivite();
            $secteur->setIntitule($alumnus['secteurActuel']);
            $secteur->setDescription('à compléter');
            $this->validateObject('New secteur', $secteur);
            $this->em->persist($secteur);
            $al->setSecteurActuel($secteur);

            $this->validateObject('New Alumnus', $al);
            $this->em->persist($al);

            $this->em->flush();
        }
        $output->writeln('Alumni: Ok');
    }

    private function createProcessus(OutputInterface $output)
    {
        foreach (self::PROCESSUS as $processus) {
            $pro = new Processus();

            $pro->setNom($processus['nom']);
            $pro->setDescription($processus['description']);

            $pil = new Personne();
            $prenom = self::PRENOM[array_rand(self::PRENOM)];
            $nom = self::NOM[array_rand(self::NOM)];
            $pil->setPrenom($prenom);
            $pil->setNom($nom);
            $pil->setEmail($prenom . '' . $nom . '@localhost.localdomain');
            $pil->setMobile('0' . rand(111111111, 999999999));
            $pil->setEmailEstValide(false);
            $pil->setEstAbonneNewsletter(false);
            $this->em->persist($pil);
            $pro->setPilote($pil);

            $this->validateObject('New Processus', $pro);
            $this->em->persist($pro);

            $this->em->flush();
        }
        $output->writeln('Processus: Ok');
    }


    private function validateObject(string $point, $object)
    {
        $constraints = $this->validator->validate($object);
        if (0 !== count($constraints)) {
            $message = 'At ' . $point;
            /** @var ConstraintViolationInterface $cs */
            foreach ($constraints as $cs) {
                $message .= ' * ' . $cs->getPropertyPath() . ' ' . $cs->getMessage();
            }

            throw new \Exception($message);
        }
    }
}
