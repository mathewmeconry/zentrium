<?php

namespace Vkaf\Bundle\OafBundle\Twig;

use Twig_Extension;
use Twig_SimpleFilter;

class Extension extends Twig_Extension
{
    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('truncate', [$this, 'truncate']),
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

    public function getName()
    {
        return 'vkaf_oaf';
    }
}
