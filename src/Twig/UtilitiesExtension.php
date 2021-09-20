<?php

namespace App\Twig;

use App\Entity\Project\Mission;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UtilitiesExtension extends AbstractExtension
{
    private $t;

    public function __construct(TranslatorInterface $t)
    {
        $this->t = $t;
    }

    public function getFilters()
    {
        return [
      new TwigFilter('ucfirst', [$this, 'ucFirst'], ['needs_environment' => true]),
      new TwigFilter('signedDate', [$this, 'signedDate']),
    ];
    }

    /**
     * Like native php ucFirst. Capitalize first letter of string.
     * A bit complex to work properly with accents.
     *
     * @return string
     */
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
     * Return the signed date of documents or the intervenant for a mission.
     * By default returns etude.non_signe translation.
     *
     * @return string
     */
    public function signedDate($doc)
    {
        $string = '';
        $dateSignature = null !== $doc->getDateSignature() ? $doc->getDateSignature()->format('d/m/y') : $this->t->trans('etude.non_signe', [], 'personne');
        $intervenant = $doc instanceof Mission ? $doc->getIntervenant() : '';

        $results = [
      'Ce' => $dateSignature,
      'Cca' => $dateSignature,
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
