<?php

namespace Zentrium\Bundle\CoreBundle\Templating\Helper;

use libphonenumber\PhoneNumber;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneNumberHelper
{
    /**
     * @var PhoneNumberUtil
     */
    private $phoneNumberUtil;

    /**
     * @var string
     */
    private $defaultRegion;

    /**
     * Constructor.
     *
     * @param PhoneNumberUtil $phoneNumberUtil
     * @param string          $defaultRegion
     */
    public function __construct(PhoneNumberUtil $phoneNumberUtil, $defaultRegion)
    {
        $this->phoneNumberUtil = $phoneNumberUtil;
        $this->defaultRegion = $defaultRegion;
    }

    /**
     * @return string
     */
    public function getDefaultRegion()
    {
        return $this->defaultRegion;
    }

    /**
     * Formats a phone number.
     *
     * @param PhoneNumber $number
     *
     * @return string|int|null $format
     */
    public function format(PhoneNumber $number, $format = null)
    {
        if ($format === null) {
            $region = $this->phoneNumberUtil->getRegionCodeForNumber($number);
            if ($region === $this->defaultRegion) {
                $format = PhoneNumberFormat::NATIONAL;
            } else {
                $format = PhoneNumberFormat::INTERNATIONAL;
            }
        } elseif (is_string($format)) {
            $format = strtoupper($format);
            if ($format === 'URL') {
                return 'tel:'.$this->phoneNumberUtil->format($number, PhoneNumberFormat::E164);
            }

            $format = constant('\libphonenumber\PhoneNumberFormat::'.$format);
        }

        return $this->phoneNumberUtil->format($number, $format);
    }
}
