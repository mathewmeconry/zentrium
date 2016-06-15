<?php

namespace Zentrium\Bundle\CoreBundle\Templating\Helper;

use League\Period\Period;
use Symfony\Component\Translation\TranslatorInterface;

class DurationHelper
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * Formats a duration in a human-readable manner.
     *
     * @param Period|int $duration
     *
     * @return string
     */
    public function format($duration)
    {
        if ($duration instanceof Period) {
            $duration = $duration->getTimestampInterval();
        }
        if (!is_int($duration) && !is_float($duration)) {
            return $duration;
        }

        $seconds = abs(round($duration));
        $hours = floor($seconds / 3600);
        $seconds -= $hours * 3600;
        $minutes = floor($seconds / 60);
        $seconds -= $minutes * 60;

        $parts = [];
        if ($hours > 0) {
            $parts[] = $this->translator->transChoice('zentrium.templating.duration.hours', $hours, ['%hours%' => $hours]);
        }
        if ($minutes > 0) {
            $parts[] = $this->translator->transChoice('zentrium.templating.duration.minutes', $minutes, ['%minutes%' => $minutes]);
        }
        if ($seconds > 0 || ($hours == 0 && $minutes == 0)) {
            $parts[] = $this->translator->transChoice('zentrium.templating.duration.seconds', $seconds, ['%seconds%' => $seconds]);
        }

        return ($duration < 0 ? '- ' : '').implode(' ', $parts);
    }
}
