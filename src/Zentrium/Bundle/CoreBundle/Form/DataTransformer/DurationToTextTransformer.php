<?php

namespace Zentrium\Bundle\CoreBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DurationToTextTransformer implements DataTransformerInterface
{
    public function transform($duration)
    {
        if (null === $duration) {
            return '';
        }

        $hours = floor($duration / 3600);
        $duration -= $hours * 3600;
        $minutes = floor($duration / 60);

        return sprintf('%d:%02d', $hours, abs($minutes));
    }

    public function reverseTransform($text)
    {
        if ($text === null || $text === '') {
            return null;
        }

        if (!preg_match('/^(-?)([0-9]+)(?::([0-9]{2}))?$/', $text, $matches)) {
            throw new TransformationFailedException('Invalid duration.');
        }

        $duration = intval($matches[2]) * 3600;
        if (isset($matches[3])) {
            $duration += intval($matches[3]) * 60;
        }

        return ($matches[1] === '-' ? -$duration : $duration);
    }
}
