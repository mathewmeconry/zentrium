<?php

namespace Vkaf\Bundle\OafBundle\Twig;

use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Twig_Extension;
use Twig_SimpleFilter;
use Twig_SimpleFunction;

class Extension extends Twig_Extension
{
    private $pushpinUrl;
    private $generator;
    private $translator;

    public function __construct($pushpinUrl, UrlGeneratorInterface $generator, TranslatorInterface $translator)
    {
        $this->pushpinUrl = $pushpinUrl;
        $this->generator = $generator;
        $this->translator = $translator;
    }

    public function getFilters()
    {
        return [
            new Twig_SimpleFilter('truncate', [$this, 'truncate']),
            new Twig_SimpleFilter('money', [$this, 'money']),
        ];
    }

    public function getFunctions()
    {
        return [
            new Twig_SimpleFunction('pushpin_url', [$this, 'pushpinUrl']),
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

    /**
     * Generates a WebSocket URL for a specific route.
     *
     * @param string $route
     * @param array  $parameters
     *
     * @return string
     */
    public function pushpinUrl($route, array $parameters = [])
    {
        if (preg_match('!^wss?://!', $this->pushpinUrl)) {
            $path = $this->generator->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_PATH);

            return rtrim($this->pushpinUrl, '/').$path;
        } else {
            $url = $this->generator->generate($route, $parameters, UrlGeneratorInterface::ABSOLUTE_URL);

            return preg_replace('!^http(s?)://([^/]+)!', 'ws$1://$2'.rtrim($this->pushpinUrl, '/'), $url);
        }
    }

    public function getName()
    {
        return 'vkaf_oaf';
    }
}
