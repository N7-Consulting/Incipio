<?php

namespace App\Twig;

use App\Entity\Project\Mission;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UtilitiesExtension extends AbstractExtension
{
  private $t;

  public function __construct(TranslatorInterface $t) {
      $this->t = $t;
  }

  public function getFilters()
  {
    return array(
      new TwigFilter(
        'ucfirst',
        array($this, 'ucFirst'),
        array('needs_environment' => true)
      ),
      new TwigFilter('soberName', [$this, 'soberName']),
    );
  }

  public function ucFirst($env, $string)
  {
    if (null !== $charset = $env->getCharset()) {
      $prefix = mb_strtoupper(mb_substr($string, 0, 1, $charset), $charset);
      $suffix = mb_substr($string, 1, mb_strlen($string, $charset));
      return sprintf('%s%s', $prefix, $suffix);
    }
    return ucfirst(strtolower($string));
  }

  /**
   * Mainly used in Archivage tab.
   * Return a sober string then __toString() method in DocTypes.
   * @return string
   */
  public function soberName($doc) {
    $string = '';
    $dateSignature = $doc->getDateSignature() !== NULL ? $doc->getDateSignature()->format('d/m/y') : $this->t->trans('etude.non_signe', [], 'personne');
    $intervenant = $doc instanceof Mission ? $doc->getIntervenant() : '';

    $results = [
      'Ce' => $dateSignature,
      'Cca' => $dateSignature,
      'Bdc' => $dateSignature,
      'Av' =>  $dateSignature,
      'ProcesVerbal' => $dateSignature,
      'Mission' => $intervenant,
    ];

    foreach ($results as $subclass => $res) {
      $class = 'App\Entity\Project\\' . $subclass;
      if ($doc instanceof $class) {
        $string = $res;
        break;
      }
    }
    return $string;
  }
}
