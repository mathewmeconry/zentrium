<?php

namespace Zentrium\Bundle\CoreBundle\Templating\Helper;

use DateTime;
use DateTimeImmutable;
use DateTimeInterface;
use Sonata\IntlBundle\Templating\Helper\DateTimeHelper as BaseDateTimeHelper;
use Symfony\Component\Translation\TranslatorInterface;

class DateTimeHelper
{
    /**
     * @var BaseDateTimeHelper
     */
    private $helper;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(BaseDateTimeHelper $helper, TranslatorInterface $translator)
    {
        $this->helper = $helper;
        $this->translator = $translator;
    }

    /**
     * Formats a duration in a human-readable manner.
     *
     * @param DateTimeInterface $date
     * @param string            $patternId
     *
     * @return string
     */
    public function format($date, $patternId)
    {
        if ($date instanceof DateTimeImmutable) {
            $timestamp = $date->getTimestamp();
            $date = new DateTime(null, $date->getTimezone());
            $date->setTimestamp($timestamp);
        }

        $pattern = $this->translator->trans('zentrium.templating.datetime.'.$patternId);

        return $this->helper->format($date, $pattern);
    }
}
