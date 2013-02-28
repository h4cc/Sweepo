<?php

namespace Sweepo\CoreBundle\Twig;

use Symfony\Component\Translation\TranslatorInterface;

class DateExtension extends \Twig_Extension
{
    /**
     * @var Symfony\Component\Translation\TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return array(
            'translate_date' => new \Twig_Filter_Method($this, 'translateDate'),
        );
    }

    public function translateDate($datetime)
    {
        error_log(var_export($datetime, true));
        $year = $datetime->format('Y');
        $day = $datetime->format('d');
        $month = $this->translator->trans(strtolower($datetime->format('F')));
        $hour = $datetime->format('H');
        $minutes = $datetime->format('i');

        return $day . ' ' . $month . ' ' . $year . ' - ' . $hour . ':' . $minutes;
    }

    public function getName()
    {
        return 'date_extension';
    }
}