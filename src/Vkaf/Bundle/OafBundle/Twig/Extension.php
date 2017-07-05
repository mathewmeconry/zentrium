<?php

namespace Vkaf\Bundle\OafBundle\Twig;

use Symfony\Component\Translation\TranslatorInterface;
use Twig_Extension;
use Twig_SimpleFilter;

class Extension extends Twig_Extension
{
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('truncate', [$this, 'truncate']),
            new Twig_SimpleFilter('money', [$this, 'money']),
        ];
    }

    /**
     * Truncates a text to a certain length.
     *
     * @param string $text   Text
     * @param int    $length Maximum length
     *
     * @return array Width of each box
     */
    public function truncate($text, $length)
    {
        if ($text === null || strlen($text) <= $length) {
            return $text;
        }

        $breakpoint = strrpos(substr($text, 0, max(0, $length - 1)), ' ');
        if ($breakpoint === false || $breakpoint === 0) {
            return '…';
        }

        return substr($text, 0, $breakpoint).' …';
    }

    /**
     * Formats a monetary value.
     *
     * @param int $cents
     *
     * @return string
     */
    public function money($cents)
    {
        return sprintf($this->translator->trans('vkaf_oaf.money'), $cents / 100);
    }

    public function getName()
    {
        return 'vkaf_oaf';
    }
}
