<?php

/*
 * This file is part of the Incipio package.
 *
 * (c) Florian Lefevre
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace mgate\SuiviBundle\Manager;

use Doctrine\ORM\EntityManager;
use mgate\SuiviBundle\Entity\Etude as Etude;
use Ob\HighchartsBundle\Highcharts\Highchart;
use Monolog\Logger;

class ChartManager /*extends \Twig_Extension*/
{
    protected $em;
    protected $tva;
    protected $etudeManager;
    protected $logger;

    public function __construct(EntityManager $em, $tva, EtudeManager $etudeManager, Logger $logger)
    {
        $this->em = $em;
        $this->tva = $tva;
        $this->etudeManager = $etudeManager;
        $this->logger = $logger;
    }

    public function getGantt(Etude $etude, $type)
    {

        // Chart
        $series = array();
        $data = array();
        $cats = array();
        $naissance = new \DateTime();
        $mort = new \DateTime();

        //Contacts Client
        if (count($etude->getClientContacts()) != 0 && $type == 'suivi') {
            foreach ($etude->getClientContacts() as $contact) {
                $date = $contact->getDate();
                if ($naissance >= $date) {
                    $naissance = clone $date;
                }
                if ($mort <= $date) {
                    $mort = clone $date;
                }

                $data[] = array('x' => count($cats), 'y' => $date->getTimestamp() * 1000,
                    'titre' => $contact->getObjet(), 'detail' => 'fait par '.$contact->getFaitPar()->getPrenomNom().' le '.$date->format('d/m/Y'), );
            }
            $series[] = array('type' => 'scatter', 'data' => $data);
            $cats[] = 'Contact client';
        }

        //Documents
        if ($type == 'suivi') {
            $data = array();
            $count_cats = count($cats);
            for ($j = 0;$j < $count_cats;++$j) {
                $data[] = array();
            }
            $dataSauv = $data;

            if ($etude->getAp() && $etude->getAp()->getDateSignature()) {
                $date = $etude->getAp()->getDateSignature();
                if ($naissance >= $date) {
                    $naissance = clone $date;
                }
                if ($mort <= $date) {
                    $mort = clone $date;
                }

                $data[] = array('x' => count($cats), 'y' => $date->getTimestamp() * 1000,
                        'titre' => 'Avant-Projet', 'detail' => 'signé le '.$date->format('d/m/Y'), );
                $series[] = array('type' => 'scatter', 'data' => $data, 'marker' => array('symbol' => 'square', 'fillColor' => 'blue'));
                $naissance = clone $etude->getAp()->getDateSignature();
            }
            $data = $dataSauv;
            if ($etude->getCc() && $etude->getCc()->getDateSignature()) {
                $date = $etude->getCc()->getDateSignature();
                if ($naissance >= $date) {
                    $naissance = clone $date;
                }
                if ($mort <= $date) {
                    $mort = clone $date;
                }

                $data[] = array('x' => count($cats), 'y' => $date->getTimestamp() * 1000,
                    'titre' => 'Convention Client', 'detail' => 'signé le '.$date->format('d/m/Y'), );
                $series[] = array('type' => 'scatter', 'data' => $data, 'marker' => array('symbol' => 'triangle', 'fillColor' => 'red'));
            }
            $data = $dataSauv;
            if ($etude->getPvr() && $etude->getPvr()->getDateSignature()) {
                $date = $etude->getPvr()->getDateSignature();
                if ($naissance >= $date) {
                    $naissance = clone $date;
                }
                if ($mort <= $date) {
                    $mort = clone $date;
                }

                $data[] = array('x' => count($cats), 'y' => $date->getTimestamp() * 1000,
                        'name' => 'Procès Verbal de Recette', 'detail' => 'signé le '.$date->format('d/m/Y'), );
                $series[] = array('type' => 'scatter', 'data' => $data, 'marker' => array('symbol' => 'circle'));
            }
            $cats[] = 'Documents';
        }

        //Etude
        if ($type == 'suivi') {
            $data = array();
            $count_cats = count($cats);
            for ($j = 0;$j < $count_cats;++$j) {
                $data[] = array();
            }

            if ($etude->getDateLancement() && $etude->getDateFin(true)) {
                $debut = $etude->getDateLancement();
                $fin = $etude->getDateFin(true);

                $data[] = array('low' => $debut->getTimestamp() * 1000, 'y' => $fin->getTimestamp() * 1000, 'color' => '#005CA4',
                        'titre' => 'Durée de déroulement des phases', 'detail' => 'du '.$debut->format('d/m/Y').' au '.$fin->format('d/m/Y'), );

                $cats[] = 'Etude';
            }
        }

        foreach ($etude->getPhases() as $phase) {
            if ($phase->getDateDebut() && $phase->getDelai()) {
                $debut = $phase->getDateDebut();
                if ($naissance >= $debut) {
                    $naissance = clone $debut;
                }

                $fin = clone $debut;
                $fin->add(new \DateInterval('P'.$phase->getDelai().'D'));
                if ($mort <= $fin) {
                    $mort = clone $fin;
                }

                $func = new \Zend\Json\Expr('function() {return this.point.titre;}');
                $data[] = array('low' => $fin->getTimestamp() * 1000, 'y' => $debut->getTimestamp() * 1000,
                    'titre' => $phase->getTitre(), 'detail' => 'du '.$debut->format('d/m/Y').' au '.$fin->format('d/m/Y'), 'color' => '#F26729',
                        'dataLabels' => array('enabled' => true, 'align' => 'left', 'inside' => true, 'verticalAlign' => 'bottom', 'formatter' => $func, 'y' => -5), );
            } else {
                $data[] = array();
            }

            $cats[] = 'Phase n°'.($phase->getPosition() + 1);
        }
        $series[] = array('type' => 'bar', 'data' => $data);

        //Today, à faire à la fin
        $data = array();
        if ($type == 'suivi') {
            $now = new \DateTime('NOW');
            //if($naissance >= $date)
                //$naissance= clone $date;
            $data[] = array('x' => 0, 'y' => $now->getTimestamp() * 1000,
                'titre' => "aujourd'hui", 'detail' => 'le '.$now->format('d/m/Y'), );
            $data[] = array('x' => count($cats) - 1, 'y' => $now->getTimestamp() * 1000,
                'titre' => "aujourd'hui", 'detail' => 'le '.$now->format('d/m/Y'), );

            $series[] = array('type' => 'spline', 'data' => $data, 'marker' => array('radius' => 1, 'color' => '#545454'), 'color' => '#545454', 'lineWidth' => 1, 'pointWidth' => 5);
        }

        $style = array('color' => '#000000', 'fontSize' => '11px', 'fontFamily' => 'Calibri (Corps)');

        $ob = new Highchart();
        $ob->chart->renderTo('ganttChart');  // The #id of the div where to render the chart
        $ob->chart->height(100 + count($etude->getPhases()) * 25);
        $ob->title->text('');
        $ob->xAxis->title(array('text' => ''));
        $ob->xAxis->categories($cats);
        $ob->xAxis->labels(array('style' => $style));
        $ob->yAxis->title(array('text' => ''));
        $ob->yAxis->type('datetime');
        $ob->yAxis->min($naissance->sub(new \DateInterval('P1D'))->getTimestamp() * 1000);
        $ob->yAxis->max($mort->add(new \DateInterval('P1D'))->getTimestamp() * 1000);
        $ob->yAxis->labels(array('style' => $style));
        $ob->chart->zoomType('y');
        $ob->credits->enabled(false);
        $ob->legend->enabled(false);
        $ob->exporting->enabled(false);
        $ob->plotOptions->series(array('pointPadding' => 0, 'groupPadding' => 0, 'pointWidth' => 10, 'groupPadding' => 0, 'marker' => array('radius' => 5), 'tooltip' => array('pointFormat' => '<b>{point.titre}</b><br /> {point.detail}')));
        $ob->plotOptions->scatter(array('tooltip' => array('headerFormat' => '')));
        $ob->series($series);

        return $ob;
    }

    public function exportGantt(Highchart $ob, $filename, $width=800)
    {
        $logger = $this->logger;

        // Create the file
        $chemin = 'tmp/'.$filename.'.json';
        $destination = 'tmp/'.$filename.'.png';

        $render = $ob->render();

        // On garde que ce qui est intéressant
        $render = strstr($render, '{', false);
        $render = substr($render, 1);
        $render = strstr($render, '{', false);

        $render = substr($render, 0, strrpos($render, '}')); // on tronque jusqu'a la dernire ,
        $render = substr($render, 0, strrpos($render, '}')); // on tronque jusqu'a la dernire ,
        $render .= '}';

        $fp = fopen($chemin, 'w');
        if ($fp) {
            if (fwrite($fp, $render) === false) {
                $logger->err("exportGantt: impossible d'écrire dans le fichier .json (".$chemin.')');

                return false;
            }

            fclose($fp);
        } else {
            $logger->err('exportGantt: impossible de créer le fichier .json ('.$chemin.')');

            return false;
        }

        $cmd = 'phantomjs js/highcharts-convert.js -infile '.$chemin.' -outfile '.$destination.' -width '.$width.' -constr Chart';
        $output = shell_exec($cmd);
        //l'execution de la commande affiche des messages de fonctionnement. On ne retient que la 3eme ligne (celle de la destination quand tout fonctionne bien).
        //Highcharts.options.parsed Highcharts.customCode.parsed tmp/gantt411ENS.png
        $temp = preg_split('#\n#',$output);
        $output = $temp[2];
        if (strncmp($output, $destination, strlen($destination)) == 0) {
            if (file_exists($destination)) {
                return true;
            } else {
                $logger->err("exportGantt: le fichier final n'existe pas (".$destination.')');

                return false;
            }
        } else {
            $logger->err("exportGantt: erreur lors de la génération de l'image: ".$output, array('cmd' => $cmd));

            return false;
        }
    }

    public function getGanttSuivi(array $etudes)
    {

        // Chart
        $series = array();
        $data = array();
        $cats = array();
        $naissance = new \DateTime();
        $mort = new \DateTime();

        //Etudes
        foreach ($etudes as $etude) {
            if ($etude->getDateLancement() && $etude->getDateFin()) {
                $debut = $etude->getDateLancement();
                $fin = $etude->getDateFin();

                if ($naissance >= $debut) {
                    $naissance = clone $debut;
                }
                if ($mort <= $fin) {
                    $mort = clone $fin;
                }

                $func = new \Zend\Json\Expr('function() {return this.point.titre;}');
                $data[] = array('low' => $fin->getTimestamp() * 1000, 'y' => $debut->getTimestamp() * 1000,
                    'titre' => $etude->getNom(), 'detail' => 'du '.$debut->format('d/m/Y').' au '.$fin->format('d/m/Y'), 'color' => '#F26729',
                        'dataLabels' => array('enabled' => true, 'align' => 'left', 'inside' => true, 'verticalAlign' => 'bottom', 'formatter' => $func, 'y' => -5), );
            } else {
                $data[] = array();
            }

            $cats[] = $etude->getReference();
        }
        $series[] = array('type' => 'bar', 'data' => $data);

        //Today, à faire à la fin
        $data = array();

        $now = new \DateTime('NOW');
        //if($naissance >= $date)
            //$naissance= clone $date;
        $data[] = array('x' => 0, 'y' => $now->getTimestamp() * 1000,
            'titre' => "aujourd'hui", 'detail' => 'le '.$now->format('d/m/Y'), );
        $data[] = array('x' => count($cats) - 1, 'y' => $now->getTimestamp() * 1000,
            'titre' => "aujourd'hui", 'detail' => 'le '.$now->format('d/m/Y'), );

        $series[] = array('type' => 'spline', 'data' => $data, 'marker' => array('radius' => 1, 'color' => '#545454'), 'color' => '#545454', 'lineWidth' => 1, 'pointWidth' => 5);

        $style = array('color' => '#000000', 'fontSize' => '11px', 'fontFamily' => 'Calibri (Corps)');
        $ob = new Highchart();
        $ob->chart->renderTo('ganttChart');  // The #id of the div where to render the chart
        $ob->title->text('');
        $ob->xAxis->title(array('text' => ''));
        $ob->xAxis->categories($cats);
        $ob->xAxis->labels(array('style' => $style));
        $ob->yAxis->title(array('text' => ''));
        $ob->yAxis->type('datetime');
        $ob->yAxis->min($naissance->sub(new \DateInterval('P1D'))->getTimestamp() * 1000);
        $ob->yAxis->max($mort->add(new \DateInterval('P1D'))->getTimestamp() * 1000);
        $ob->yAxis->labels(array('style' => $style));
        $ob->chart->zoomType('y');
        $ob->credits->enabled(false);
        $ob->legend->enabled(false);
        $ob->exporting->enabled(false);
        $ob->plotOptions->series(array('pointPadding' => 0, 'groupPadding' => 0, 'pointWidth' => 10, 'groupPadding' => 0, 'marker' => array('radius' => 5), 'tooltip' => array('pointFormat' => '<b>{point.titre}</b><br /> {point.detail}')));
        $ob->plotOptions->scatter(array('tooltip' => array('headerFormat' => '')));
        $ob->series($series);

        return $ob;
    }
}
