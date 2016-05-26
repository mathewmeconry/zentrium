<?php

namespace Zentrium\Bundle\CoreBundle\Twig;

use Symfony\Component\Translation\TranslatorInterface;
use Zentrium\Bundle\CoreBundle\Templating\Helper\PhoneNumberHelper;

class Extension extends \Twig_Extension
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var PhoneNumberHelper
     */
    private $phoneNumberHelper;

    /**
     * Constructor.
     *
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator, PhoneNumberHelper $phoneNumberHelper)
    {
        $this->translator = $translator;
        $this->phoneNumberHelper = $phoneNumberHelper;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new \Twig_SimpleFilter('without', [$this, 'withoutFilter']),
            new \Twig_SimpleFilter('formatList', [$this, 'formatListFilter']),
            new \Twig_SimpleFilter('phoneNumber', [$this->phoneNumberHelper, 'format']),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('distributeGrid', [$this, 'distributeGridFunction']),
        ];
    }

    /**
     * Returns an array without certain values.
     *
     * @param  array $array
     * @param  mixed $exclude
     * @return array
     */
    public function withoutFilter($array, $exclude)
    {
        $filteredArray = [];

        if (!is_array($exclude)) {
            $exclude = [$exclude];
        }

        foreach ($array as $key => $value) {
            if (!in_array($value, $exclude)) {
                $filteredArray[$key] = $value;
            }
        }

        return $filteredArray;
    }

    /**
     * Returns a list as human-readable list.
     *
     * @param  mixed  $list
     * @param  array  $options
     * @return string
     */
    public function formatListFilter($list, array $options = [])
    {
        if (!count($list)) {
            return '';
        }

        $options = array_merge([
            'property' => null,
            'translate' => false,
            'quotes' => ['', ''],
            'relation' => 'and',
        ], $options);
        if (is_string($options['quotes'])) {
            $options['quotes'] = [$options['quotes'], $options['quotes']];
        }

        $translator = $this->translator;

        $strings = array_map(function ($object) use ($options, $translator) {
            if ($options['property'] === null) {
                $string = (string) $object;
            } elseif (is_array($object)) {
                $string = (string) $object[$options['property']];
            } else {
                $method = 'get'.ucfirst($options['property']);
                $string = (string) $object->$method();
            }
            if ($options['translate']) {
                $string = $translator->trans($string);
            }

            return $options['quotes'][0].$string.$options['quotes'][1];
        }, $list);

        $last = array_pop($strings);

        if (count($strings)) {
            $relationString = $translator->trans('zentrium.twig.format_list.'.$options['relation']);

            return sprintf('%s %s %s', implode(', ', $strings), $relationString, $last);
        } else {
            return $last;
        }
    }

    /**
     * Distributes boxes in a grid such that every row is filled completely.
     *
     * @param  int   $columns  Number of columns
     * @param  int   $minWidth Minimum width of each box
     * @param  int   $boxes    Number of boxes
     * @return array Width of each box
     */
    public function distributeGridFunction($columns, $minWidth, $boxes)
    {
        $distributor = new GridDistributor($columns, $minWidth);

        return $distributor->distribute($boxes);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'zentrium';
    }
}
