<?php

namespace Zentrium\Bundle\CoreBundle\Form\DataTransformer;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberUtil;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Zentrium\Bundle\CoreBundle\Templating\Helper\PhoneNumberHelper;

/**
 * Phone number to string transformer.
 */
class PhoneNumberToStringTransformer implements DataTransformerInterface
{
    /**
     * @var PhoneNumberUtil
     */
    private $util;

    /**
     * @var PhoneNumberHelper
     */
    private $helper;

    /**
     * Constructor.
     *
     * @param PhoneNumberUtil   $util
     * @param PhoneNumberHelper $helper
     */
    public function __construct(PhoneNumberUtil $util, PhoneNumberHelper $helper)
    {
        $this->util = $util;
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function transform($phoneNumber)
    {
        if (null === $phoneNumber) {
            return '';
        } elseif (false === $phoneNumber instanceof PhoneNumber) {
            throw new TransformationFailedException('Expected a \libphonenumber\PhoneNumber.');
        }

        return $this->helper->format($phoneNumber);
    }

    /**
     * {@inheritdoc}
     */
    public function reverseTransform($string)
    {
        if (!$string) {
            return null;
        }

        try {
            return $this->util->parse($string, $this->helper->getDefaultRegion());
        } catch (NumberParseException $e) {
            throw new TransformationFailedException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
